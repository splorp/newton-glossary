<?= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">'; ?>
<?= '<?xml-stylesheet type="text/xsl" href="' . url('sitemap.xsl') . '"?>'; ?>
	<channel>
		<title><?= $site->title() ?> — Latest Additions</title>
		<description>Recent terms and sources added to the glossary.</description>
		<link><?= $site->url() ?>/latest</link>
		<atom:link href="<?= $page->url() ?>/feed" rel="self" type="application/rss+xml" />
		<?php foreach($pages as $page): ?>
		<item>
			<title><?= $page->title()->xml() ?></title>
			<description><?= $page->text()->kirbytext()->xml() ?></description>
			<pubDate><?= $page->date()->toDate('r') ?></pubDate>
			<link><?= $page->url() ?></link>
			<guid isPermaLink="true"><?= $page->url() ?></guid>
		</item>
		<?php endforeach ?>
	</channel>
</rss>
