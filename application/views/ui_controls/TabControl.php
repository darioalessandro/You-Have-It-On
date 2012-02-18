<div class="ui_tab_control" style="width:<?php echo $width_control; ?>px;">
	<div class="tab_parent">
	<?php
	$subitems = ''; 
	foreach($items as $key => $item){ ?>
		<a href="<?php echo $item["url"];?>" rel="<?php echo $key;?>" class="<?php echo $class_item.($item_select==$key? ' sel': '').' '.$item['action'];?>">
			<?php echo (isset($item["icon"])? '<img src="'.$item["icon"].'" width="12" height="12">': '').$item["label"]; ?>
		</a>
	<?php
		if(isset($item["subitems"])){
			if(count($item["subitems"]) > 0){
				$subitems .= '<div id="tchild'.$key.'" class="tab_childs"'.($item_select==$key? ' style="display:block;"': '').'>';
				foreach($item["subitems"] as $key2 => $subitem){
					$subitems .= '<a href="'.$subitem["url"].'" class="clink'.($key2==0? ' sel': '').'">'.$subitem["label"].'</a>';
				}
				$subitems .= '<div class="clear"></div>
				</div>';
			}
		}
	}?>
		<div class="clear"></div>
	</div>
	<?php echo $subitems; ?>
</div>