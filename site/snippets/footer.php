		<footer>
			<nav>
				<ul>
					<li><a href="/about">About the Newton Glossary</a></li>
					<li><a href="/changes">Change Log</a></li>
				</ul>
			</nav>
			<?php echo kirbytext($site->copyright() . "<br />" . $site->colophon()) ?>
		</footer>
		<?php echo snippet('ga'); ?>
	</body>
</html>
