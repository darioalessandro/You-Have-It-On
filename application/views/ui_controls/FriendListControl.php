<?php 
$cont_cols = 1;
$width_control = 170;
?>
<div id="ui_friends_content" style="width:<?php echo $width_control*$num_cols;?>px;">
	<div class="ui_friends_title"><?php echo $titulo; ?></div>
	<div class="friends_content" style="width:<?php echo (($width_control+3)*3)*$num_cols;?>px; left: -<?php echo ($width_control-10)*$num_cols;?>px;">
		<div class="ponaqui" style="width:<?php echo ($width_control-10)*$num_cols;?>px; left: <?php echo ($width_control-10)*$num_cols;?>px;">
<?php
	$this->load->model("user_model");
	$actionbar = new ActionBarControl();
	
	$res_user_id = $params["user_id"];
if(is_array($items))
	foreach($items as $key => $item){
		$class_cols = '';
		if($cont_cols < $num_cols || $cont_cols==1)
			$class_cols = $cont_cols==1? ' fborder'.($num_cols==1?' nob':''):' border';
		if($cont_cols == $num_cols)
			$cont_cols = 0;
		$cont_cols++;
		
		$height_items = '';
		if($view_actionbar){
			$data["items"] = array();
			$params["user_id"] = $item["id"];
			if($this->user_model->isMyFriend($params)){
				$data["items"][] = array(
					"url"		=> "javascript:void(0);",
					"name"		=> lang("remove_friends"),
					"action"	=> "remove_friend"
				);
				if($item['last_postyle'] != '')
					$data["items"][] = array(
						"url"		=> Sys::lml_get_url('postyle', $item['last_postyle']),
						"name"		=> lang("see_last_postyle"),
						"action"	=> "see_last_postyle"
					);
			}else{
				$data["items"][] = array(
					"url"		=> "javascript:void(0);",
					"name"		=> lang("add_friends"),
					"action"	=> "add_friend"
				);
			}
		}else
			$height_items = "height:50px;";
	?>
		<div<?php echo ($id?' id="'.$id."-".$key.'"':'');?><?php echo ($class?' class="'.$class.$class_cols.'"':'');?> style="width:<?php echo $width_control-20; ?>px;<?php echo $height_items; ?>">
			<?php echo '<img src="'.(isset($item["images"][0]["file_name"])? $item["images"][3]["file_name"]: '').'" class="img img_friend" width="35" height="35">'; ?>
			
			<div class="user_content" style="width:<?php echo $width_control-66; ?>px;">
				<a href="<?php echo Sys::lml_get_url('user', $item["id"]); ?>" class="link_blue"><?php echo $item["name"]." ".$item["last_name"];?></a>
			</div>
			<div class="clear"></div>
			<?php
			if($view_actionbar){
				$actionbar->ini($data);
				$actionbar->printHtml(true);
			}
			?>
			<input type="hidden" id="user_id" value="<?php echo $item['id'];?>">
			<input type="hidden" id="last_postyle" value="<?php echo $item['last_postyle'];?>">
			<div class="clear"></div>
		</div>
	<?php
		}
	?>
		<div class="clear"></div>
		</div>
	</div>
	<div class="clear"></div>
	<div id="ui_pagination" class="ui_pagination">
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
	<div class="ver_mas">
		<?php if($show_link_more){?>
		<a href="<?php echo Sys::lml_get_url("more_friends", $res_user_id)?>&lang=<?php echo $lang; ?>" class="link_ver_mas"><?php echo lang("see_all"); ?></a>
		<?php }?>
	</div>
	<input type="hidden" id="me_id" value="<?php echo $params['token_user_id'];?>">
	<input type="hidden" id="list_user_id" value="<?php echo $res_user_id;?>">
	<input type="hidden" id="view_actionbar" value="<?php echo ($view_actionbar? '1': '0'); ?>">
	<input type="hidden" id="nums_cols" value="<?php echo $num_cols; ?>">
	<input type="hidden" id="per_page" value="<?php echo $params["result_items_per_page"]; ?>">
	<input type="hidden" id="width_control" value="<?php echo $width_control; ?>">
</div>