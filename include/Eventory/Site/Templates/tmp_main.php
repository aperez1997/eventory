<html>
<head>
<title><?php echo $title;?></title>
	<LINK href="styles.css" rel="stylesheet" type="text/css">
<script src="jquery.min.js"></script>
<script src="/js/ZeroClipboard.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.4.9/angular.min.js"></script>
<script type="text/javascript">
ZeroClipboard.config( { moviePath: '/js/ZeroClipboard.swf' } );
</script>
</head>
<body>

<!-- nav -->
<?php include __DIR__ . '/tmp_nav.php'; ?>

<!-- content -->
<div class="content">
<?php echo $mainContent; ?>
</div>

</body>
</html>
