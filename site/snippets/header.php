<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta name="description" content="<?php if(isset($page_description)) { echo html($page_description); } else { echo html($site->description()); } ?>" />
		<meta name="keywords" content="<?php echo html($site->keywords()) ?>" />
		<meta name="robots" content="index, follow" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title><?php echo html($site->title()) ?> - <?php echo html($page->title()) ?></title>
		<link rel="shortcut icon" href="/favicon.ico" />
		<link rel="apple-touch-icon-precomposed" sizes="57x57" href="/assets/images/apple-touch-icon-57x57-precomposed.png" />
		<link rel="apple-touch-icon-precomposed" sizes="72x72" href="/assets/images/apple-touch-icon-72x72-precomposed.png" />
		<link rel="apple-touch-icon-precomposed" sizes="76x76" href="/assets/images/apple-touch-icon-76x76-precomposed.png" />
		<link rel="apple-touch-icon-precomposed" sizes="114x114" href="/assets/images/apple-touch-icon-114x114-precomposed.png" />
		<link rel="apple-touch-icon-precomposed" sizes="120x120" href="/assets/images/apple-touch-icon-120x120-precomposed.png" />
		<link rel="apple-touch-icon-precomposed" sizes="144x144" href="/assets/images/apple-touch-icon-144x144-precomposed.png" />
		<link rel="apple-touch-icon-precomposed" sizes="152x152" href="/assets/images/apple-touch-icon-152x152-precomposed.png" />
		<meta name="msapplication-TileImage" content="/assets/images/msapplication-tileimage-144x144.png" />
		<meta name="msapplication-TileColor" content="#009900" />
		<?php echo css('assets/styles/styles.css?2016102104') ?>
	</head>

	<body>
		<header>
			<h1><a href="<?php echo url() ?>"><?php echo html($site->title()) ?></a></h1>
			<p><?php echo html($site->description()) ?></p>
		</header>
