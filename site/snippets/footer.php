		<footer>
			<nav>
				<ul>
					<li><a href="/about" title="<?php echo (page('about')->description()) ?>">About the Newton Glossary</a></li>
					<li><a href="/changes" title="<?php echo (page('changes')->description()) ?>">Change Log</a></li>
					<li><a href="/sitemap.xml" title="An XML listing of all pages found on this site.">Sitemap</a></li>
				</ul>
			</nav>
			<?php echo kirbytext($site->copyright() . "<br />" . $site->colophon()) ?>
		</footer>
		<?php echo snippet('ga'); ?>
	</body>
</html>
