const o = {
  scanner: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M5.67127 4.25705L13.4142 12L12 13.4142L8.55382 9.96803C8.20193 10.5635 8 11.2582 8 12C8 14.2091 9.79086 16 12 16C14.2091 16 16 14.2091 16 12C16 9.87494 14.3429 8.13693 12.2503 8.00771L10.4459 6.20323C10.9416 6.07067 11.4625 6 12 6C15.3137 6 18 8.68629 18 12C18 15.3137 15.3137 18 12 18C8.68629 18 6 15.3137 6 12C6 10.7042 6.41079 9.50428 7.10925 8.52347L5.68014 7.09436C4.62708 8.44904 4 10.1513 4 12C4 16.4183 7.58172 20 12 20C16.4183 20 20 16.4183 20 12C20 7.58172 16.4183 4 12 4C10.8915 4 9.83557 4.22547 8.8757 4.63306L7.37443 3.13179C8.75768 2.40883 10.3311 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 8.87842 3.43029 6.09091 5.67127 4.25705Z"></path></svg>'
};
panel.plugin("scottboms/link-scanner", {
  icons: o,
  components: {
    "k-broken-links-view": {
      props: {
        initialLatest: {
          type: Object,
          default: () => ({
            hasScanned: !1,
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
            isRunning: !1,
            isComplete: !1,
            cancelRequested: !1,
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
          required: !0
        },
        stopUrl: {
          type: String,
          required: !0
        },
        completeUrl: {
          type: String,
          required: !0
        },
        statusUrl: {
          type: String,
          required: !0
        }
      },
      data() {
        return {
          isBusy: !1,
          latest: this.normalizeLatest(this.initialLatest),
          current: this.normalizeCurrent(this.initialCurrent),
          pollTimer: null,
          hasShownError: !1,
          completingKeys: []
        };
      },
      computed: {
        isRunning() {
          return this.current.isRunning === !0;
        },
        isStopping() {
          return this.current.isRunning === !0 && this.current.cancelRequested === !0;
        },
        runButtonIcon() {
          return this.isRunning === !0 ? "loader" : "search";
        },
        runButtonText() {
          return this.isRunning === !0 ? "Scanning…" : "Start Scan";
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
          return this.latest.results.map((t) => ({
            id: this.resultKey(t),
            result: t,
            url: {
              href: t.url,
              text: t.url
            },
            page: t.pageTitle,
            edit: {
              href: this.absolutePanelUrl(t.panelUrl),
              text: "Edit"
            },
            options: this.rowOptions(t)
          }));
        },
        progressText() {
          return this.current.totalPages < 1 ? "Preparing scan…" : this.isStopping === !0 ? `Stopping after ${this.current.processedPages} of ${this.current.totalPages} pages` : `${this.current.processedPages} of ${this.current.totalPages} pages scanned`;
        },
        progressPercent() {
          return this.current.totalPages < 1 ? 0 : Math.max(
            0,
            Math.min(100, Math.round(this.current.processedPages / this.current.totalPages * 100))
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
        this.current.isRunning === !0 && this.beginPolling();
      },
      beforeUnmount() {
        this.stopPolling();
      },
      methods: {
        resultKey(t) {
          return [
            t?.url ?? "",
            t?.pageTitle ?? "",
            t?.panelUrl ?? "",
            t?.reason ?? ""
          ].join("|");
        },
        isCompleting(t) {
          return this.completingKeys.includes(this.resultKey(t));
        },
        rowOptions(t) {
          return [
            {
              icon: this.isCompleting(t) ? "loader" : "check",
              text: this.isCompleting(t) ? "Completing…" : "Complete",
              click: "complete",
              disabled: this.isCompleting(t)
            }
          ];
        },
        absolutePanelUrl(t) {
          if (typeof t != "string" || t.length === 0)
            return "";
          const e = /^https?:\/\//i.test(t) ? new URL(t) : new URL(t, window.location.origin), r = "/pages/", n = e.pathname.indexOf(r);
          if (n !== -1) {
            const i = e.pathname.slice(0, n + r.length), s = e.pathname.slice(n + r.length);
            s.includes("/") && s.includes("+") === !1 && (e.pathname = i + s.replaceAll("/", "+"));
          }
          return e.toString();
        },
        normalizeLatest(t) {
          return {
            hasScanned: !!t?.hasScanned,
            scannedAt: typeof t?.scannedAt == "string" && t.scannedAt.length > 0 ? t.scannedAt : null,
            totalBrokenLinks: Number(t?.totalBrokenLinks || 0),
            totalCheckedLinks: Number(t?.totalCheckedLinks || 0),
            results: Array.isArray(t?.results) ? t.results : []
          };
        },
        normalizeCurrent(t) {
          return {
            id: typeof t?.id == "string" ? t.id : null,
            isRunning: !!t?.isRunning,
            isComplete: !!t?.isComplete,
            cancelRequested: !!t?.cancelRequested,
            startedAt: typeof t?.startedAt == "string" && t.startedAt.length > 0 ? t.startedAt : null,
            finishedAt: typeof t?.finishedAt == "string" && t.finishedAt.length > 0 ? t.finishedAt : null,
            stoppedAt: typeof t?.stoppedAt == "string" && t.stoppedAt.length > 0 ? t.stoppedAt : null,
            workerStartedAt: typeof t?.workerStartedAt == "string" && t.workerStartedAt.length > 0 ? t.workerStartedAt : null,
            processedPages: Number(t?.processedPages || 0),
            totalPages: Number(t?.totalPages || 0),
            currentPageTitle: typeof t?.currentPageTitle == "string" && t.currentPageTitle.length > 0 ? t.currentPageTitle : null,
            lastError: typeof t?.lastError == "string" && t.lastError.length > 0 ? t.lastError : null,
            updatedAt: typeof t?.updatedAt == "string" && t.updatedAt.length > 0 ? t.updatedAt : null
          };
        },
        // Date formatting
        formatDate(t, e = {}) {
          if (typeof t != "string" || t.length === 0)
            return "";
          const r = new Date(t);
          return Number.isNaN(r.getTime()) ? t : new Intl.DateTimeFormat(void 0, e).format(r);
        },
        formatSummaryDate(t) {
          return this.formatDate(t, {
            month: "2-digit",
            day: "2-digit",
            year: "numeric"
          });
        },
        formatStatusDate(t) {
          return this.formatDate(t, {
            hour: "numeric",
            minute: "2-digit",
            second: "2-digit"
          });
        },
        formatSummaryTime(t) {
          return this.formatDate(t, {
            hour: "numeric",
            minute: "2-digit"
          });
        },
        formatStoppedDate(t) {
          return this.formatDate(t, {
            dateStyle: "medium",
            timeStyle: "short"
          });
        },
        async requestJson(t, e = "GET", r = null) {
          const n = {
            Accept: "application/json",
            "X-CSRF": window.panel.system.csrf
          };
          r !== null && (n["Content-Type"] = "application/json");
          const i = await fetch(t, {
            method: e,
            credentials: "same-origin",
            headers: n,
            body: r !== null ? JSON.stringify(r) : null
          }), s = await i.text();
          let a = {};
          if (s.length > 0)
            try {
              a = JSON.parse(s);
            } catch {
              throw new Error(s.slice(0, 300));
            }
          if (!i.ok || a?.status === "error")
            throw new Error(a?.message || "The link scan failed.");
          return a;
        },
        applyPayload(t) {
          this.current = this.normalizeCurrent(t?.current), this.latest = this.normalizeLatest(t?.latest);
        },
        beginPolling() {
          this.stopPolling(), this.pollTimer = window.setInterval(() => {
            this.refreshStatus(!1);
          }, 5e3);
        },
        stopPolling() {
          this.pollTimer && (window.clearInterval(this.pollTimer), this.pollTimer = null);
        },
        showError(t) {
          this.$panel.notification.error({
            message: t
          });
        },
        maybeShowWorkerError() {
          this.current.lastError && this.hasShownError === !1 && (this.hasShownError = !0, this.showError(this.current.lastError));
        },
        // Start scanning pages for broken links
        async startScan() {
          this.isBusy = !0, this.hasShownError = !1;
          try {
            const t = await this.requestJson(this.startUrl, "POST");
            this.applyPayload(t), this.current.isRunning === !0 && this.beginPolling();
          } catch (t) {
            this.showError(t?.message || "Could not start the scan.");
          } finally {
            this.isBusy = !1;
          }
        },
        // Stop scanning pages for broken links
        async stopScan() {
          this.isBusy = !0;
          try {
            const t = await this.requestJson(this.stopUrl, "POST");
            this.applyPayload(t), this.current.isRunning === !0 ? this.beginPolling() : this.stopPolling();
          } catch (t) {
            this.showError(t?.message || "Could not stop the scan.");
          } finally {
            this.isBusy = !1;
          }
        },
        async completeResult(t) {
          const e = this.resultKey(t);
          if (this.completingKeys.includes(e) !== !0) {
            this.completingKeys = [...this.completingKeys, e];
            try {
              const r = await this.requestJson(this.completeUrl, "POST", {
                url: t.url,
                pageTitle: t.pageTitle,
                panelUrl: t.panelUrl,
                reason: t.reason
              });
              this.applyPayload(r);
            } catch (r) {
              this.showError(r?.message || "Could not mark the row as complete.");
            } finally {
              this.completingKeys = this.completingKeys.filter((r) => r !== e);
            }
          }
        },
        async onTableOption(t, e) {
          t === "complete" && e?.result && await this.completeResult(e.result);
        },
        // Refresh scanning status
        async refreshStatus(t = !0) {
          t === !0 && (this.isBusy = !0);
          try {
            const e = await this.requestJson(this.statusUrl, "GET");
            this.applyPayload(e), this.current.isRunning === !0 ? this.beginPolling() : this.stopPolling(), this.maybeShowWorkerError();
          } catch (e) {
            this.stopPolling(), t === !0 && this.showError(e?.message || "Could not refresh scan status.");
          } finally {
            t === !0 && (this.isBusy = !1);
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
