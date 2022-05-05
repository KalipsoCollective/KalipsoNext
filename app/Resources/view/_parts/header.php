<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?php echo $title; ?></title>
		<link rel="stylesheet" type="text/css" href="<?php echo KN\Helpers\Base::assets('libs/bootstrap/bootstrap.min.css'); ?>">
		<link rel="stylesheet" type="text/css" href="<?php echo KN\Helpers\Base::assets('libs/kalipsotable/kalipso.table.css'); ?>">
		<link rel="stylesheet" type="text/css" href="<?php echo KN\Helpers\Base::assets('css/kalipso.next.css'); ?>">
	</head>
	<body id="wrap">
		<?php echo \KN\Helpers\Base::sessionStoredAlert(); ?>