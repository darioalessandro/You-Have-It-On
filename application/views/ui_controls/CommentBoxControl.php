<div class="comments" style="width:<?php echo $width_box; ?>px;">
	<?php 
		$actionbar = new ActionBarControl();
		
		echo ($titulo!=''? '<div id="'.$id_titulo.'" class="'.$class_titulo.'">'.$titulo.'</div>':'')
	?>
	<?php foreach($items["comments"] as $key => $item){ ?>
	<div<?php echo ($id?' id="'.$id."-".$key.'"':'');?><?php echo ($class?' class="'.$class.'"':'');?>>
		<img src="<?php echo (count($item["images"])>0? $item["images"][0]["sizes"][3]["url"]:''); ?>" width="30" height="30"
			class="<?php echo ($key % 2 == 0? 'img': 'img2');?>">
		<div class="<?php echo ($key % 2 == 0? 'comm_conte': 'comm_conte2');?>" style="width:<?php echo $width_box-80; ?>px;"><?php
			echo '<a href="'.Sys::lml_get_url('user', $item["user_id"]).'&lang='.$lang.'" class="link_blue">'.$item["name"].' '.$item["last_name"].'</a> '; 
			echo $item['comment'];?>
			<span class="<?php echo ($key % 2 == 0? 'comm_conte_pic': 'comm_conte_pic2');?>"></span>
		</div>
		<div class="clear"></div>
		<span class="<?php echo ($key % 2 == 0? 'date_comment': 'date_comment2');?>"><?php echo $item["date_added"];?></span>
		<?php
		$data = array("items" => array(), "class_bar" => 'action_bar '.($key % 2 == 0? 'bardel': 'bardel2'));
		
		if($item["can_be_deleted"] == '1')
			$data["items"][] = array(
				"url"		=> "javascript:void(0);",
				"name"		=> lang('delete'),
				"action"	=> "comm_delete"
			);
		$actionbar->ini($data);
		$actionbar->printHtml(true);
		?>
		<div class="clear"></div>
		<input type="hidden" id="comment_id" value="<?php echo $item['comment_id'];?>">
		<input type="hidden" id="action_method" value="<?php echo $action_method;?>">
	</div>
	<?php } ?>
	<div class="clear" style="height:4px;"></div>
	<div class="bar_comment">
	<?php
		$data = array("items" => array(), "class_bar" => 'action_bar');
		
		$no_show = ' no_show';
		if($link_all_comment=='1' && count($items["comments"]) < $items["total_rows"])
			$no_show = '';
		
		if($is_of_postyle){	//si son comentarios de un postyle, le ponemos esta funcionalidad
			$data["items"][] = array(
				"url"		=> "javascript:void(0);",
				"name"		=> lang('rankear'),
				"action"	=> "rankear"
			);
			
			if($params["can_be_deleted"] == '1'){ //tiene permisos para eliminar el postyle
				$data["items"][] = array(
					"url"		=> "javascript:void(0);",
					"name"		=> lang('delete'),
					"action"	=> "postyle_delete"
				);
			}
		}
			
		if($can_post && $show_comment_textbox == false)
			$data["items"][] = array(
				"url"		=> "javascript:void(0);",
				"name"		=> lang('comment'),
				"action"	=> "acomment",
				"class"		=> ($no_show!=''? ' no_border': '')
			);
		
		$data["items"][] = array(
				"url"		=> "javascript:void(0);",
				"name"		=> lang('see_all'),
				"action"	=> "link_all_comment link_ver_mas",
				"class"		=> $no_show
			);
		$actionbar->ini($data);
		$actionbar->printHtml(true);
	?>
		<div class="clear"></div>
	</div>
	<?php if($can_post){ ?>
	<form id=frm_comment class="frm_comment" style="display: <?php echo ($show_comment_textbox? 'block': 'none')?>;" onsubmit="return false;">
		<span class="title"><?php echo lang("add_comment"); ?></span>
		<?php 
		$this->load->model("user_model");
		$data_user = $this->user_model->getInfoUser($params);
		?>
		<div class="add_comment">
			<img src="<?php echo (count($data_user["user"]["images"])>0? $data_user["user"]["images"][0]["sizes"][3]["url"]: ''); ?>" width="40" height="40" class="img">
			<div class="comm_conte" style="width:<?php echo $width_box-80; ?>px;">
				<textarea name="txt_comment" class="txt_comment expand50-200" rows="3" cols="60"></textarea>
				<span class="comm_conte_pic"></span>
			</div>
			<div class="clear"></div>
		</div>
		<input type="submit" name="sub_comment" value="<?php echo lang("comment"); ?>" class="sub_comment">
		<div class="clear"></div>
	</form>
	<?php }?>
	<div class="clear"></div>
	<input type="hidden" id="action_method" value="<?php echo $action_method;?>">
	<input type="hidden" id="content_parent" value="<?php echo $content_parent;?>">
	<input type="hidden" id="id_obj_comment" value="<?php echo $id_obj_comment;?>">
</div>