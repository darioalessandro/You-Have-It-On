<div class="ads_control">
	<?php 
		echo ($title!=''? '<div class="title">'.$title.'</div>': ''); 
		
		list($width, $height, $type, $attr) = getimagesize($url_img);
		$porcent	= $width_img*100/$width;
		$width		= $width_img;
		$height		= $height*$porcent/100;
		
		$a_ini = '';
		$a_fin = '';
		if($url_link!=''){
			$a_ini = '<a href="'.$url_link.'" target="_blank">';
			$a_fin = '</a>';
		}
	?>
	<?php echo $a_ini;?>
	<img src="<?php echo $url_img; ?>" width="<?php echo $width;?>" height="<?php echo $height;?>">
	<?php echo $a_fin;?>
	<div class="text"><?php
		echo (strlen($text)>200? substr($text, 0, 187)."..." : $text); 
	?></div>
</div>
