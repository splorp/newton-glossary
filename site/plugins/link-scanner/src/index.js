import { icons } from "./icons.js";

panel.plugin("scottboms/link-scanner", {
	icons,
	components: {
		"k-broken-links-view": {
			props: {
				initialLatest: {
					type: Object,
					default: () => ({
						hasScanned: false,
						scannedAt: null,
						totalBrokenLinks: 0,
						totalCheckedLinks: 0,
						results: []
					})
				},
				initialCurrent: {
					type: Object,
					default: () => ({
						id: null,
						isRunning: false,
						isComplete: false,
						cancelRequested: false,
						startedAt: null,
						finishedAt: null,
						stoppedAt: null,
						workerStartedAt: null,
						processedPages: 0,
						totalPages: 0,
						currentPageTitle: null,
						lastError: null,
						updatedAt: null
					})
				},
				startUrl: {
					type: String,
					required: true
				},
				stopUrl: {
					type: String,
					required: true
				},
				completeUrl: {
					type: String,
					required: true
				},
				statusUrl: {
					type: String,
					required: true
				}
			},
			data() {
				return {
					isBusy: false,
					latest: this.normalizeLatest(this.initialLatest),
					current: this.normalizeCurrent(this.initialCurrent),
					pollTimer: null,
					hasShownError: false,
					completingKeys: []
				};
			},
			computed: {
				isRunning() {
					return this.current.isRunning === true;
				},
				isStopping() {
					return this.current.isRunning === true && this.current.cancelRequested === true;
				},
				runButtonIcon() {
					return this.isRunning === true ? "loader" : "search";
				},
				runButtonText() {
					return this.isRunning === true ? "Scanning…" : "Start Scan";
				},
				columns() {
					return {
						url: {
							label: "Broken Link",
							type: "url"
						},
						page: {
							label: "Page Title"
						},
						edit: {
							label: " ",
							type: "url",
							width: "5rem"
						}
					};
				},
				hasTableOptions() {
					return this.rows.length > 0;
				},
				rows() {
					return this.latest.results.map((result) => ({
						id: this.resultKey(result),
						result,
						url: {
							href: result.url,
							text: result.url
						},
						page: result.pageTitle,
						edit: {
							href: this.absolutePanelUrl(result.panelUrl),
							text: "Edit"
						},
						options: this.rowOptions(result)
					}));
				},
				progressText() {
					if (this.current.totalPages < 1) {
						return "Preparing scan…";
					}

					if (this.isStopping === true) {
						return `Stopping after ${this.current.processedPages} of ${this.current.totalPages} pages`;
					}

					return `${this.current.processedPages} of ${this.current.totalPages} pages scanned`;
				},
				progressPercent() {
					if (this.current.totalPages < 1) {
						return 0;
					}

					return Math.max(
						0,
						Math.min(100, Math.round((this.current.processedPages / this.current.totalPages) * 100))
					);
				},
				progressFillStyle() {
					return {
						position: "absolute",
						inset: "0 auto 0 0",
						width: `${this.progressPercent}%`,
						background: "repeating-linear-gradient(45deg, #b1d864, #b1d864 10px, #719726 10px, #719726 20px)",
						transition: "width 240ms ease"
					};
				}
			},
			created() {
				if (this.current.isRunning === true) {
					this.beginPolling();
				}
			},
			beforeUnmount() {
				this.stopPolling();
			},
			methods: {
				resultKey(result) {
					return [
						result?.url ?? "",
						result?.pageTitle ?? "",
						result?.panelUrl ?? "",
						result?.reason ?? ""
					].join("|");
				},
				isCompleting(result) {
					return this.completingKeys.includes(this.resultKey(result));
				},
				rowOptions(result) {
					return [
						{
							icon: this.isCompleting(result) ? "loader" : "check",
							text: this.isCompleting(result) ? "Completing…" : "Complete",
							click: "complete",
							disabled: this.isCompleting(result)
						}
					];
				},
				absolutePanelUrl(url) {
					if (typeof url !== "string" || url.length === 0) {
						return "";
					}

					const absolute = /^https?:\/\//i.test(url)
						? new URL(url)
						: new URL(url, window.location.origin);

					const marker = "/pages/";
					const index = absolute.pathname.indexOf(marker);

					if (index !== -1) {
						const prefix = absolute.pathname.slice(0, index + marker.length);
						const suffix = absolute.pathname.slice(index + marker.length);

						if (suffix.includes("/") && suffix.includes("+") === false) {
							absolute.pathname = prefix + suffix.replaceAll("/", "+");
						}
					}

					return absolute.toString();
				},
				normalizeLatest(state) {
					return {
						hasScanned: Boolean(state?.hasScanned),
						scannedAt:
							typeof state?.scannedAt === "string" && state.scannedAt.length > 0
								? state.scannedAt
								: null,
						totalBrokenLinks: Number(state?.totalBrokenLinks || 0),
						totalCheckedLinks: Number(state?.totalCheckedLinks || 0),
						results: Array.isArray(state?.results) ? state.results : []
					};
				},
				normalizeCurrent(state) {
					return {
						id: typeof state?.id === "string" ? state.id : null,
						isRunning: Boolean(state?.isRunning),
						isComplete: Boolean(state?.isComplete),
						cancelRequested: Boolean(state?.cancelRequested),
						startedAt:
							typeof state?.startedAt === "string" && state.startedAt.length > 0
								? state.startedAt
								: null,
						finishedAt:
							typeof state?.finishedAt === "string" && state.finishedAt.length > 0
								? state.finishedAt
								: null,
            stoppedAt:
              typeof state?.stoppedAt === "string" && state.stoppedAt.length > 0
                ? state.stoppedAt
                : null,
            workerStartedAt:
              typeof state?.workerStartedAt === "string" && state.workerStartedAt.length > 0
                ? state.workerStartedAt
                : null,
						processedPages: Number(state?.processedPages || 0),
						totalPages: Number(state?.totalPages || 0),
						currentPageTitle:
							typeof state?.currentPageTitle === "string" && state.currentPageTitle.length > 0
								? state.currentPageTitle
								: null,
						lastError:
							typeof state?.lastError === "string" && state.lastError.length > 0
								? state.lastError
								: null,
						updatedAt:
							typeof state?.updatedAt === "string" && state.updatedAt.length > 0
								? state.updatedAt
								: null
					};
				},

				// Date formatting
				formatDate(value, options = {}) {
					if (typeof value !== "string" || value.length === 0) {
						return "";
					}

					const date = new Date(value);

          if (Number.isNaN(date.getTime())) {
            return value;
          }

					return new Intl.DateTimeFormat(undefined, options).format(date);
				},

				formatSummaryDate(value) {
				  return this.formatDate(value, {
				    month: "2-digit",
						day: "2-digit",
						year: "numeric"
				  });
				},

				formatStatusDate(value) {
					return this.formatDate(value, {
						hour: "numeric",
						minute: "2-digit",
						second: "2-digit"
					});
				},

				formatSummaryTime(value) {
					return this.formatDate(value, {
						hour: "numeric",
						minute: "2-digit"
					});
				},

				formatStoppedDate(value) {
					return this.formatDate(value, {
						dateStyle: "medium",
						timeStyle: "short"
					});
				},
				async requestJson(url, method = "GET", body = null) {
					const headers = {
						Accept: "application/json",
						"X-CSRF": window.panel.system.csrf
					};

					if (body !== null) {
						headers["Content-Type"] = "application/json";
					}

					const response = await fetch(url, {
						method,
						credentials: "same-origin",
						headers,
						body: body !== null ? JSON.stringify(body) : null
					});

					const payloadText = await response.text();
					let payload = {};

					if (payloadText.length > 0) {
						try {
							payload = JSON.parse(payloadText);
						} catch (error) {
							throw new Error(payloadText.slice(0, 300));
						}
					}

					if (!response.ok || payload?.status === "error") {
						throw new Error(payload?.message || "The link scan failed.");
					}
					return payload;
				},

				applyPayload(payload) {
					this.current = this.normalizeCurrent(payload?.current);
					this.latest = this.normalizeLatest(payload?.latest);
				},

				beginPolling() {
					this.stopPolling();
					this.pollTimer = window.setInterval(() => {
						this.refreshStatus(false);
					}, 5000);
				},

				stopPolling() {
					if (this.pollTimer) {
						window.clearInterval(this.pollTimer);
						this.pollTimer = null;
					}
				},

				showError(message) {
					this.$panel.notification.error({
						message
					});
				},

				maybeShowWorkerError() {
					if (this.current.lastError && this.hasShownError === false) {
						this.hasShownError = true;
						this.showError(this.current.lastError);
					}
				},

				// Start scanning pages for broken links
				async startScan() {
					this.isBusy = true;
					this.hasShownError = false;

					try {
						const payload = await this.requestJson(this.startUrl, "POST");
						this.applyPayload(payload);

						if (this.current.isRunning === true) {
							this.beginPolling();
						}
					} catch (error) {
						this.showError(error?.message || "Could not start the scan.");
					} finally {
						this.isBusy = false;
					}
				},

				// Stop scanning pages for broken links
				async stopScan() {
					this.isBusy = true;
					try {
						const payload = await this.requestJson(this.stopUrl, "POST");
						this.applyPayload(payload);

						if (this.current.isRunning === true) {
							this.beginPolling();
						} else {
							this.stopPolling();
						}
					} catch (error) {
						this.showError(error?.message || "Could not stop the scan.");
					} finally {
						this.isBusy = false;
					}
				},
				async completeResult(result) {
					const key = this.resultKey(result);

					if (this.completingKeys.includes(key) === true) {
						return;
					}

					this.completingKeys = [...this.completingKeys, key];

					try {
						const payload = await this.requestJson(this.completeUrl, "POST", {
							url: result.url,
							pageTitle: result.pageTitle,
							panelUrl: result.panelUrl,
							reason: result.reason
						});
						this.applyPayload(payload);
					} catch (error) {
						this.showError(error?.message || "Could not mark the row as complete.");
					} finally {
						this.completingKeys = this.completingKeys.filter((currentKey) => currentKey !== key);
					}
				},
				async onTableOption(option, row) {
					if (option === "complete" && row?.result) {
						await this.completeResult(row.result);
					}
				},

				// Refresh scanning status
				async refreshStatus(showErrors = true) {
					if (showErrors === true) {
						this.isBusy = true;
					}
					try {
						const payload = await this.requestJson(this.statusUrl, "GET");
						this.applyPayload(payload);

						if (this.current.isRunning === true) {
							this.beginPolling();
						} else {
							this.stopPolling();
						}

						this.maybeShowWorkerError();
					} catch (error) {
						this.stopPolling();

						if (showErrors === true) {
							this.showError(error?.message || "Could not refresh scan status.");
						}
					} finally {
						if (showErrors === true) {
							this.isBusy = false;
						}
					}
				}
			},

			// Vue panel template
			template: `
			<k-panel-inside>
				<k-view class="k-broken-links-view">
					<k-header class="k-site-view-header">
						Link Scanner
						<k-button-group slot="buttons">
							<k-button
								:disabled="isBusy || isRunning"
								:icon="runButtonIcon"
								:text="runButtonText"
								size="sm"
								theme="green"
								variant="filled"
								@click="startScan"
							/>
							<k-button
								v-if="current.isRunning === true"
								:disabled="isBusy || isStopping"
								:icon="isStopping ? 'loader' : 'cancel'"
								:text="isStopping ? 'Stopping…' : 'Stop Scan'"
								size="sm"
								theme="negative"
								variant="filled"
								@click="stopScan"
							/>
							<k-button
								:disabled="isBusy"
								icon="refresh"
								text="Refresh"
								size="sm"
								theme="passive"
								variant="filled"
								@click="refreshStatus(true)"
							/>
						</k-button-group>
					</k-header>

					<k-box v-if="current.lastError" theme="negative" style="margin-bottom: var(--spacing-4);">
						{{ current.lastError }}
					</k-box>

					<k-box v-if="current.stoppedAt" theme="passive" icon="info" style="margin-bottom: var(--spacing-4);">
						Scan stopped at {{ formatStoppedDate(current.stoppedAt) }}.
					</k-box>

					<k-box v-if="latest.hasScanned === false && isRunning === false" icon="search" theme="info" style="margin-bottom: var(--spacing-4);">
						No results yet. Start or restart a scan to check for broken links.
					</k-box>

					<k-box v-if="current.isComplete === true && rows.length === 0" icon="check" theme="positive" style="margin-bottom: var(--spacing-4);">
						No broken links were found.
					</k-box>

					<div v-if="latest.hasScanned === true && isRunning === false" data-variant="columns" class="k-grid k-sections" style="margin-bottom: var(--spacing-4)">
						<div class="k-column" style="--width: 1/1">
							<dl data-size="huge" class="k-stats">
								<div data-theme="info" class="k-stat">
									<dt class="k-stat-label">Unique Links</dt>
									<dd class="k-stat-value">{{ latest.totalCheckedLinks }}</dd>
									<dd class="k-stat-info">Scan Results</dd>
								</div>

								<div data-theme="info" class="k-stat">
									<dt class="k-stat-label">Broken Links</dt>
									<dd class="k-stat-value">{{ latest.totalBrokenLinks }}</dd>
									<dd class="k-stat-info">404/500 Responses</dd>
								</div>

								<div v-if="latest.scannedAt" data-theme="info" class="k-stat">
									<dt class="k-stat-label">at {{ formatSummaryTime(latest.scannedAt) }}</dt>
									<dd class="k-stat-info">Last Scan</dd>
									<dd class="k-stat-value">{{ formatSummaryDate(latest.scannedAt) }}</dd>
								</div>
							</dl>
						</div>
					</div>

					<k-box v-if="isRunning" class="k-progress-info" theme="white" style="margin-bottom: var(--spacing-4)">
						<k-box icon="clock" v-if="current.startedAt">Started at {{ formatStatusDate(current.startedAt) }}</k-box>
						<!--<k-box v-if="current.workerStartedAt">Worker {{ formatStatusDate(current.workerStartedAt) }}</k-box>-->
						<k-box icon="refresh" v-if="current.updatedAt">Last update: {{ formatStatusDate(current.updatedAt) }}</k-box>
						<k-box icon="url" v-if="current.currentPageTitle"><b>Page:</b> {{ current.currentPageTitle }}</k-box>
						<k-box icon="cancel" v-if="current.cancelRequested">Stop requested.</k-box>
					</k-box>

					<div v-if="isRunning" style="margin-top: var(--spacing-4);">
						<div class="k-progress-container">
							<div :style="progressFillStyle"></div>
							<div class="k-progress-bar">{{ progressText }}</div>
						</div>
					</div>

					<k-table
						v-else
						:columns="columns"
						:rows="rows"
						:options="hasTableOptions"
						empty="No broken links found."
						@option="onTableOption"
					/>
					</k-view>
				</k-panel-inside>`
		}
	}
});
