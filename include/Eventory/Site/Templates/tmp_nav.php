<ol class='nav'>
	<?php foreach ($navItems as $navItemArr){

		$href = reset($navItemArr);
		$text = next($navItemArr);
		$blank = next($navItemArr);
		$confirm = next($navItemArr);
	?>
	<li>
		<a href="<?php echo $href; ?>" 
<?php if ($blank) { ?> target="_blank" <?php }; ?>
<?php if ($confirm){ ?> onclick="return confirm('Are you sure?');" <?php }; ?>
><?php echo $text; ?>
</a>
</li>
	<?php } ?>

</ol>
