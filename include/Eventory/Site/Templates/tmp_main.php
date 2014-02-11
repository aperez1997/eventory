<html>
<head>
<title><?php echo $title;?></title>
	<LINK href="styles.css" rel="stylesheet" type="text/css">
<script src="jquery.min.js"></script>
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
