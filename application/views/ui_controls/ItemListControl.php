<?php 
$class_cle = '';
$width_col = 165;
$width_col_bog = 525;
$img_wh = '70';
$class_det = ' mini';
if($detailed_item){
	$width_col = 310;
	$width_col_bog = 960;
	
	$img_wh = '45';
	$class_det = '';
}
?>

<div id="ui_item_content" style="width:<?php echo $width_col*$num_cols;?>px;">
	<div class="title">
		<span class="list_title"><?php echo $categorys["name"];?></span> 
		<span class="item_back" cat_back=""><<</span>
	</div>
	<div class="items_content" style="width:<?php echo $width_col_bog*$num_cols;?>px; left: -<?php echo $width_col*$num_cols;?>px;">
		<div class="ponaqui" style="width:<?php echo $width_col*$num_cols;?>px; left: <?php echo $width_col*$num_cols;?>px;">
<?php
	$this->load->model("user_model");
	$actionbar = new ActionBarControl();
	$clikes = new LikeControl();
	
	$class_openwin = '';
	if($open_detail_win_click)
		$class_openwin = ' itm_details';
	
	if($total_items > 0){
		foreach($items as $key => $item){
			$class_cle = (($key % $num_cols) == 0)? ' clear': '';
	?>
		<div<?php echo ($id?' id="'.$id."-".$key.'"':'');?><?php echo ($class?' class="'.$class.$class_det.$class_cle.$class_openwin.'"':'');?>>
			<?php echo '<img src="'.(isset($item["images"][0]["file_name"])? $item["images"][0]['sizes'][3]["url"]: '').'" class="img itm_details img_item'.$class_det.'" width="'.$img_wh.'" height="'.$img_wh.'">'; ?>
			
			<div class="item_content">
				<?php echo $item["label"].
					(count($item["brand"])>0? 
						' <a href="javascript:void(0);" class="link_blue preview_brand" rel="'.$item["brand"]["id"].'">'.
						$item["brand"]["name"].'</a>': '');?>
			</div>
			<div class="clear"></div>
			<?php
				if($detailed_item == true){
					$clikes->ini(array("i_like" => $item["like"]));
					$clikes->printHtml(true);
					
					$data["items"] = array(
						array(
							"url"		=> "javascript:void(0);",
							"name"		=> lang("details"),
							"action"	=> "itm_details"
						));
					if(isset($item['store']["id"]))	
						$data["items"][] = array(
							"url"		=> "javascript:void(0);",
							"name"		=> lang("purchased_in"),
							"action"	=> "preview_store"
						);
					
					$data["items"][] = array(
							"url"		=> Sys::lml_get_url("item", $item['id']),
							"name"		=> lang("comment"),
							"action"	=> ""
						);
					
					$actionbar->ini($data);
					$actionbar->printHtml(true);
				}
			?>
			<input type="hidden" id="item_id" value="<?php echo $item['id'];?>">
			<input type="hidden" id="store_id" value="<?php echo (isset($item['store']["id"])? $item['store']["id"]:'');?>">
			<input type="hidden" id="me_id" value="<?php echo $params['token_user_id'];?>">
		</div>
	<?php 
			}
		}else
			echo '<div class="error_msg">'.lang("no_items_cat").'
			</div>';
	?>
		<div class="clear"></div>
		</div>
	</div>
	<div class="clear"></div>
	<div id="ui_pagination_itm" class="ui_pagination">
	<?php 
		$ci =& get_instance();
		$ci->load->library('pagination');
	
		$paginar['base_url'] 		= '';
		$paginar['anchor_class']	= $pagination_class;
		$paginar['total_rows'] 		= $total_items;
		$paginar['per_page'] 		= $params["result_items_per_page"];
		$paginar['cur_page']		= $params["result_page"]-1;
		$paginar['javascript']		= 'javascript:void(0);';
		$paginar['first_link']		= false;
		$paginar['last_link']		= false;
		$paginar['display_pages']	= false;
		
		$ci->pagination->initialize($paginar);
		echo $ci->pagination->create_links();
	?>
	</div>
	<input type="hidden" id="category_id" value="<?php echo $params['filter_category_id'];?>">
	<input type="hidden" id="tme_id" value="<?php echo $params['token_user_id'];?>">
	<input type="hidden" id="per_pag" value="<?php echo $params['result_items_per_page'];?>">
	<input type="hidden" id="num_cols" value="<?php echo $num_cols;?>">
	<input type="hidden" id="cat_per_page" value="<?php echo $cat_per_page;?>">
	<input type="hidden" id="cat_num_cols" value="<?php echo $num_cols;?>">
	<input type="hidden" id="detailed_item" value="<?php echo ($detailed_item? '1': '0');?>">
	<input type="hidden" id="open_detail_win_click" value="<?php echo ($open_detail_win_click? '1': '0');?>">
	<div class="clear"></div>
	
	<div class="subcats_title"><?php echo lang("sub_categorys"); ?></div>
	<div class="items_subcats" style="width:<?php echo $width_col_bog*$num_cols;?>px; left: -<?php echo $width_col*$num_cols;?>px;">
		<div class="catponaqui" style="width:<?php echo $width_col*$num_cols;?>px; left: <?php echo $width_col*$num_cols;?>px;">
	<?php
		$ci->load->model('category_model');
		$param_cats = array(
			"consumer_key" 				=> $params['consumer_key'],
			"oauth_token" 				=> $params['oauth_token'],
			"result_items_per_page" 	=> $cat_per_page,
			"result_page"				=> 1,
			"filter_category_parent"	=> $params['filter_category_id']
		);
		$data_cats = $ci->category_model->getCategories($param_cats);
		
		$paginar['total_rows'] 		= $data_cats["total_rows"];
		$paginar['per_page'] 		= $param_cats["result_items_per_page"];
		$paginar['cur_page']		= 0;
		
		foreach($data_cats["categories"] as $key => $cat){
	?>
		<div class="item_subcat" cat_id="<?php echo $cat["id"];?>"><?php echo $cat["name"]; ?></div>
	<?php } ?>
		</div>
	</div>
	<div class="clear"></div>
	<div id="pagination_cat" class="ui_pagination">
	<?php
		$ci->pagination->initialize($paginar);
		echo $ci->pagination->create_links(); 
	?>
	</div>
</div>