		<footer>
			<nav>
				<ul>
					<li><a href="/about" alt="How this whole thing works, plus a little bit of history">About the Newton Glossary</a></li>
					<li><a href="/changes" alt="A chronological list of major releases, updates, and improvements">Change Log</a></li>
					<li><a href="/sitemap.xml" alt="An XML listing of all pages found on this site">Sitemap</a></li>
				</ul>
			</nav>
			<?php echo kirbytext($site->copyright() . "<br />" . $site->colophon()) ?>
		</footer>
		<?php echo snippet('ga'); ?>
	</body>
</html>
