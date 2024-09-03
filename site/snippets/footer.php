		<footer>
			<nav>
				<ul>
					<li>Version <?php echo ($site->version()) ?></li>
					<li><a href="<?php echo ($site->url()) ?>/about" title="<?php echo (page('about')->description()) ?>">About the Newton Glossary</a></li>
					<li><a href="<?php echo ($site->url()) ?>/changes" title="<?php echo (page('changes')->description()) ?>">Changes</a></li>
					<li><a href="<?php echo ($site->url()) ?>/latest/feed" title="Subscribe to a feed of the latest terms and sources added to the glossary.">RSS</a></li>
					<li><a href="<?php echo ($site->url()) ?>/sitemap" title="An XML listing of all pages found on this site.">Sitemap</a></li>
				</ul>
			</nav>
			<?php echo $site->colophon()->kirbytext() ?>
		</footer>
		<?php echo snippet('ga'); ?>
	</body>
</html>
