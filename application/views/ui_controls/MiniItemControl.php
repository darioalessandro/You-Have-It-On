<div class="ui_mini_items">
<?php
	$clikes = new LikeControl();
	
	$item_mcar = 15;
	$brand_mcar = 30;
	$type_control = 1; //1:items, 2:users
	foreach($items as $key => $item){
		$type_control = isset($item['user_name'])? 2: 1;
		$txt = $type_control==1? $item["item_label"]: $item['user_name'];
		$txt_brand = $type_control==1? $item["brand_name"]: '';
		$len_itm = strlen($txt);
		$len_bran = strlen($txt_brand);
		
		$title_brand = $title_item = '';
		if($len_bran+$len_itm > $item_mcar+$brand_mcar){
			if($len_bran > $brand_mcar){
				$txt_brand = substr($txt_brand, 0, $brand_mcar-3).'...';
				$title_brand = ' title="'.$txt_brand.'"';
			}
			if($len_itm > $item_mcar){
				$txt_item = substr($txt, 0, $item_mcar-3).'...';
				$title_item = ' title="'.$txt.'"';
			}
		}else{
			//$txt_brand = $item["brand_name"];
			$txt_item = $txt;
		}
		if($type_control==1){
			echo '<div class="mini_item" id_item="'.$item["item_id"].'">
				<div class="imini_info" obj_type="item" obj_id="'.$item["item_id"].'" me_id="'.$params["user_id"].'">
					<span class="item_label"'.$title_item.'>'.$txt_item.'</span> 
					<a href="javascript:void(0);" class="link_blue preview_brand"'.$title_brand.' rel="'.$item["brand_id"].'" me_id="'.$params["user_id"].'">'.$txt_brand.'</a> 
					<span class="num_likes">'.$item["nums_likes"].'</span>
				</div>
				<div class="imini_like">';
			
			$clikes->ini(array("i_like" => $item["like"]));
			$clikes->printHtml(true);
			
			echo '
					<div class="clear"></div>
				</div>
				<input type="hidden" id="item_id" value="'.$item["item_id"].'">
				<input type="hidden" id="me_id" value="'.$params["user_id"].'">
			</div>';
		}else{
			if($item['user_status']=='1')
				$htm_info = '<a href="'.Sys::lml_get_url('user', $item["user_id"]).'" class="link_blue"'.$title_item.'>'.$txt_item.'</a>';
			else
				$htm_info = '<span class="item_label"'.$title_item.'>'.$txt_item.'</span>';
				
			echo '<div class="mini_usritem" id_user="'.$item["user_id"].'">
				<div class="imini_info">
					'.$htm_info.' 
				</div>
				<input type="hidden" id="user_id" value="'.$item["user_id"].'">
				<input type="hidden" id="me_id" value="'.$params["user_id"].'">
			</div>';
		}
	}
?>
	<div class="clear"></div>
</div>