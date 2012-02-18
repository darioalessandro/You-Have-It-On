	<div class="footer margin">
		<?php
		$url = explode("?", Sys::getCurrentUrl());
		$url[1] = isset($url[1])? preg_replace('/([?|&]lang=(\S{3})|lang=(\S{3}))/', '', $url[1]): '';
		$url[0] .= (strpos($url[0], "?") === false)? '?' : '&';
		?>
		<a href="javascript:clang.set('<?php echo $url[0];?>lang=eng<?php echo $url[1]; ?>', 'eng');" class="link_blue<?php echo $lang=="eng"?' sel':''; ?>">English</a> &bull; 
		<a href="javascript:clang.set('<?php echo $url[0];?>lang=spa<?php echo $url[1]; ?>', 'spa');" class="link_blue<?php echo $lang=="spa"?' sel':''; ?>">Español</a> 
	</div>
	<div class="footer margin1">
		<a href="<?php echo base_url();?>info/terms?lang=<?php echo $lang; ?>" class="link_blue"><?php echo lang("terms"); ?></a> &bull; 
		<a href="<?php echo base_url();?>info/terms?lang=<?php echo $lang; ?>" class="link_blue"><?php echo lang("privacy"); ?></a> &bull;
		<span>&copy; 2011 <?php echo Sys::$title_web; ?></span>
	</div>
</body>
</html>