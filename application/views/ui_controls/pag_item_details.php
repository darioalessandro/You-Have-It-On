<?php
$width_items_gall = 180;
$border_gall = 2;
$num_items_gall = count($data["items"][0]["images"]);
?>
<div id="pag_item_detail">
	<div class="pag_title"><?php echo $data["items"][0]["label"]?></div>
	<div id="pitem_detail">
		<div class="itm_detail_conte">
			<div class="itm_conte_left" style="width: <?php echo $width_items_gall; ?>px;">
				<div class="ui_gallery" style="width: <?php echo $width_items_gall+$border_gall; ?>px;">
					<div class="uigary_modal" style="width: <?php echo ($width_items_gall+2+$border_gall)*$num_items_gall; ?>px; height:<?php echo $width_items_gall; ?>px;">
					<?php foreach($data["items"][0]["images"] as $key => $item){ ?>
						<span class="uigary_item" style="width: <?php echo $width_items_gall; ?>px; height:<?php echo $width_items_gall; ?>px;">
							<img id="img_gallery16<?php echo $key; ?>" src="<?php echo $item["sizes"][1]["url"];?>">
						</span>
					<?php }?>
						<div class="clear"></div>
					</div>
					<div class="clear"></div>
					<div id="ui_pagination_gall16" class="ui_pagination">
						<div class="pages">
					<?php 
						$ci =& get_instance();
						$ci->load->library('pagination');
					
						$paginar['base_url'] 		= '';
						$paginar['anchor_class']	= "pag_gallery";
						$paginar['total_rows'] 		= $num_items_gall;
						$paginar['per_page'] 		= '1';
						$paginar['cur_page']		= 0;
						$paginar['javascript']		= 'javascript:void(0);';
						$paginar['first_link']		= false;
						$paginar['last_link']		= false;
						$paginar['display_pages']	= false;
						
						$ci->pagination->initialize($paginar);
						echo $ci->pagination->create_links();
					?>
						</div>
						<div class="clear"></div>
					</div>
				</div>
				<div class="clear"></div>
				<?php
					$clikes = new LikeControl();
					$clikes->ini(array("i_like" => $data["items"][0]["like"]));
					$clikes->printHtml(true); 
				?>
			</div>
			<div class="itm_conte_right" style="width: 325px;">
				<div class="itm_con_data"><strong>Titulo:</strong> <?php echo $data["items"][0]["label"]?></div>
				<?php 
					echo (count($data["items"][0]["brand"])!=0? 
						'<div class="itm_con_data"><strong>'.lang("brand").':</strong> <a href="javascript:void(0);" class="link_blue preview_brand close" rel="'.
						$data["items"][0]["brand"]["id"].'" close="item_detail" me_id="'.$token_user_id.'">'.$data["items"][0]["brand"]["name"].'</a></div>': '');
					echo (isset($data["items"][0]["price"])? 
							'<div class="itm_con_data"><strong>'.lang("price").':</strong> '.$data["items"][0]["price"].'</div>': '');
					echo (count($data["items"][0]["store"])!=0? 
							'<div class="itm_con_data"><strong>'.lang("purchased_in").':</strong> <a href="javascript:void(0);" class="link_blue preview_store close" rel="'.
							$data["items"][0]["store"]["id"].'" close="item_detail" me_id="'.$token_user_id.'">'.$data["items"][0]["store"]["name"].'</a></div>': '');
				?>
				<div class="itm_con_data"><strong>Caracteristicas</strong>
					<div class="clear"></div>
				<?php 
					foreach($data["items"][0]["attributes"] as $key => $cat){
						echo '<div class="itm_con_subdata"><strong>'.
							$cat["name"].':</strong> <span>&nbsp;'.
							$cat["value"].'</span></div>';
					}
				?>
				</div>
			</div>
			<div class="clear"></div>
		</div>	
		<div class="clear"></div>
		<input type="hidden" id="item_id" value="<?php echo $data["items"][0]["id"];?>">
		<input type="hidden" id="me_id" value="<?php echo $token_user_id; ?>">
	</div>
	
	<?php
	$this->load->model("item_model"); 
	$comments = new CommentBoxControl();
	
	
	$params = array(
		"item_id" => $data["items"][0]["id"],
		"lang"=>					$lang,
		"consumer_key"=>			$consumer_key,
		"oauth_token"=>				$oauth_token,
		"result_items_per_page"=>	'4',
		"result_page"=>				'1',
		"link_all_comment"=>		'1',
		"token_user_id"=>			$token_user_id
	);
	$data_commen = $this->item_model->get_comments($params);
	
	$dasdata = array(
		"titulo" 			=> "",
		"comments"			=> $data_commen,
		"id" 				=> "ui_comments",
		"class" 			=> "comment",
		"link_all_comment"	=> $params["link_all_comment"],
		"action_method"		=> 'item',
		"params"			=> $params,
		"width_box"			=> 525,
		"content_parent"	=> 'div#pag_item_detail',
		"id_obj_comment"	=> 'item_id'
	);
	$comments->ini($dasdata);
	$comments->printHtml(true);
	?>
	<input type="hidden" id="item_id" value="<?php echo $data["items"][0]["id"];?>">
	<input type="hidden" id="num_imgs_items" value="<?php echo count($data["items"][0]["images"]);?>">
</div>
