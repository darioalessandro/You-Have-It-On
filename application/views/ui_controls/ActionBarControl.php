<ul class="<?php echo $class_bar; ?>">
<?php
	$num = count($items)-1; 
	foreach($items as $key => $item){
		$no_border = $key==$num? ' no_border': ''; 
	?>
	<li<?php echo ($id?' id="'.$id."-".$key.'"':'');?><?php echo ($class?' class="'.$class.(isset($item['class'])?$item['class']:'').$no_border.'"':'');?>>
		<a href="<?php echo $item["url"];?>" class="<?php echo $class_item.' '.$item['action'];?>"><?php echo $item['name'];?></a>
	</li>
<?php }?>
</ul>