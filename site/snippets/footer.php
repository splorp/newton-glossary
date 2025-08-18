		<footer>
			<nav>
				<ul>
					<li>Version <?php echo $site->release() ?></li>
					<li><a href="<?php echo $site->url() ?>/about" title="<?php echo page('about')->description() ?>"<?php echo (page('about')->isOpen()) ? ' class="active"' : '' ?>>About the Newton Glossary</a></li>
					<li><a href="<?php echo $site->url() ?>/changes" title="<?php echo page('changes')->description() ?>"<?php echo (page('changes')->isOpen()) ? ' class="active"' : '' ?>>Changes</a></li>
					<li><a href="<?php echo $site->url() ?>/latest/feed" title="Subscribe to a feed of the latest terms and sources added to the glossary.">RSS</a></li>
					<li><a href="<?php echo $site->url() ?>/sitemap" title="An XML listing of all pages found on this site.">Sitemap</a></li>
				</ul>
			</nav>
			<?php echo $site->colophon()->kirbytext() ?>
		</footer>
	</body>
</html>
