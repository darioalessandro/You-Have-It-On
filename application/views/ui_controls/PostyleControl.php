<?php
	$actionbar = new ActionBarControl();
	$comments = new CommentBoxControl();
	$clikes = new LikeControl();
	$mini_item = new MiniItemControl();
	$this->load->model("postyle_model");
	
	foreach($items as $key => $item){ ?>
<div<?php echo ($id?' id="'.$id."-".$key.'"':'');?><?php echo ($class?' class="'.$class.'"':'');?>>
	<?php
	
	//datos de la imagen 
	$width_img = 200;
	$width = $height = $width_img;
	$style = '';
	
	if(isset($item["images"][0])){
    	$urlii = str_replace(base_url(), "", $item["images"][0]["sizes"][1]["url"]);
		list($width, $height, $type, $attr) = @getimagesize($urlii);
		
		$porcent	= $width_img*100/$width;
		$width		= $width_img;
		$height		= intval($height*$porcent/100);
		
		if($height > 400){
			$porcent	= 400*100/$height;
			$height		= 400;
			$width		= intval($width_img*$porcent/100);
			
			$style = ' style="margin-left:'.intval(($width_img-$width)/2).'px;"';
		}
	}
	
	echo '<div class="postyle_left">
			<span>'.(isset($item["images"][0])? 
				'<a href="'.Sys::lml_get_url("postyle", $item['id']).'&lang='.$lang.'"><img src="'.$item["images"][0]["sizes"][1]["url"].'"'.$style.' 
				class="img img_postyle" width="'.$width.'" height="'.$height.'"></a>': '').
			'</span>
		</div>'; ?>
	
	<div class="postyle_content">
		<div class="postyle_descrip">
		<?php 
			echo (isset($item["user_images"][0])? 
				'<img src="'.$item["user_images"][0]["sizes"][3]["url"].'" class="img" width="30" height="30">': '')
		?>
			<span>
				<a href="<?php echo Sys::lml_get_url("user", $item["user_id"]).'&lang='.$lang; ?>" class="link_blue lpost_usr">
					<?php echo $item["user_name"]." ".$item["user_last_name"]?></a>: 
				<?php echo $item["description"];?>
			</span>
			<div class="clear"></div>
		</div>
		
		<div class="postyle_ocati_date">
			<span><?php echo lang("ocation")?>:</span> <?php echo $item["ocation_name"];?> | 
			<span><?php echo lang("date")?>:</span> <?php echo $item["date_added"];?>
		</div>
		
		<?php
		//Colocamos los items del postyle con el control mini_items
		$mini_item->ini(array(
			"params"		=> $params,
			"items"			=> $item["tags"],
			"width_control"	=> 530
		));
		$mini_item->printHtml(true);
		
		/* $num = ceil(count($item["tags"])/$num_cols);
		 $coun = $num;
		 $text = '';
		 
		 foreach($item["tags"] as $key2 => $tag){
		 	$text .= '
			 	<li'.($id_lis?' id="'.$id_lis."-".$key2.'"':'').($class_lis?' class="'.$class_lis.'"':'').'>
					'.$tag['item_label'].'
				</li>';
		 	
			if(($key2+1) >= $coun){
				echo '
			<ul>
				'.$text.'
			</ul>';
				$text = '';
				$coun += $num;
			}
		}*/
		?>
		
	</div>
	<div class="clear"></div>
	<?php 
		$clikes->ini(array("i_like" => $item["i_like"]));
		$clikes->printHtml(true);
		
		/*$data = array(
			"items" => array(
				/*array(
					"url"		=> "javascript:void(0);",
					"name"		=> lang('comment'),
					"action"	=> "acomment"
				),
				array(
					"url"		=> "javascript:void(0);",
					"name"		=> lang('rankear'),
					"action"	=> "rankear"
				)
			)
		);
		$actionbar->ini($data);
		$actionbar->printHtml(true);*/
		
	?>
	<div class="clear"></div>
	<?php
	$params["postyle_id"] = $item['id'];
	$params["token_user_id"] = $params["user_id"];
	$params["can_be_deleted"] = $item["can_be_deleted"];
	$data_commen = $this->postyle_model->get_comments($params);
	
	$data = array(
		"titulo" 			=> "",
		"comments"			=> $data_commen,
		"id" 				=> "ui_comments",
		"class" 			=> "comment",
		"link_all_comment"	=> $params["link_all_comment"],
		"action_method"		=> 'postyle',
		"params"			=> $params,
		"width_box"			=> $width_commentbox,
		"is_of_postyle"		=> true
	);
	$comments->ini($data);
	$comments->printHtml(true);
	?>
	<input type="hidden" id="postyle_id" value="<?php echo $item['id'];?>">
	<input type="hidden" id="postyle_user_id" value="<?php echo $item['user_id'];?>">
</div>
<?php }?>