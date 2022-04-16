<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title><?php echo $title; ?></title>
		<style>
		body {
			font-family: ui-monospace, 
				Menlo, Monaco, 
				"Cascadia Mono",
				"Segoe UI Mono", 
				"Roboto Mono", 
				"Oxygen Mono", 
				"Ubuntu Monospace", 
				"Source Code Pro",
				"Fira Mono", 
				"Droid Sans Mono", 
				"Courier New", monospace;
			background: #151515;
			color: #b2b2b2;
			padding: 1rem;
		}
		h1 {
			margin: 0;
			color: #bebebe;
		}
		h2 {
			margin: 0;
			color: #777;
		}
		</style>
	</head>
	<body>
		<h1><?php echo $error; ?></h1>
		<h2><?php echo $output; ?></h2>
	</body>
</html>