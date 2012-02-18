<div id="home" class="margin_top">
	<div class="col_content dtlpostyle margin_right">
		<div id="postyle_conte_detail">
		<?php
		$width_img = 662;
		$url_im = '';
		$img_width = $img_height = 0;
		$html_tags = $style = '';
		$margin = 0;
		if(count($data["postyles"][0]["images"]) > 0){
			$urlii = str_replace(base_url(), "", $data["postyles"][0]["images"][0]["sizes"][0]["url"]);
			list($img_width, $img_height, $type, $attr) = @getimagesize($urlii);
			
			$porcent	= $width_img*100/$img_width;
			$img_width	= $width_img;
			$img_height	= intval($img_height*$porcent/100);
			
			if($img_height > 600){
				$porcent	= 600*100/$img_height;
				$img_height	= 600;
				$img_width	= intval($width_img*$porcent/100);
				
				$margin = intval(($width_img-$img_width)/2);
				$style = ' margin-left:'.$margin.'px;';
			}
			
			$url_im = $data["postyles"][0]["images"][0]["sizes"][0]["url"];
			
			//generamos los tags para mostrarse en la imagen
			if(is_array($data["postyles"][0]["tags"])){
				foreach($data["postyles"][0]["tags"] as $key => $item){
					$top = intval($item["y"]*$img_height/100);
					$left = intval($item["x"]*$img_width/100)+$margin;
					$tg_width = intval($item["width"]*$img_width/100);
					$tg_height = intval($item["height"]*$img_height/100);
					if(isset($item["item_id"])){
						$tag_id = $item["item_id"];
						$tag_label = $item["item_label"];
					}else{
						$tag_id = $item["user_id"];
						$tag_label = $item["user_name"];
					}
					
					$html_tags .= '<div id="tag_imgs'.$tag_id.'" class="tag_imgs no_border" 
						style="top:'.$top.'px; left:'.$left.'px; width:'.$tg_width.'px; height:'.$tg_height.'px;"><div class="border2">
						<span>'.$tag_label.'</span></div></div>';
				}
			}
		}
		
			echo '
			<div class="dtlpost_img" style="width:'.$width_img.'px;height:'.$img_height.'px">
				<img alt="" src="'.$url_im.'" width="'.$img_width.'" height="'.$img_height.'" style="position:absolute;'.$style.'">
				'.$html_tags.'
			</div>
			<div class="postyle_ocati_date">
				<span>'.lang("ocation").'</span> '.$data["postyles"][0]["ocation_name"].'
				<span>'.lang("date").'</span> '.$data["postyles"][0]["date_added"].'
			</div>
			<div class="postyle_descrip">';
			echo (isset($data["postyles"][0]["user_images"][0])? 
				'<img src="'.$data["postyles"][0]["user_images"][0]["sizes"][3]["url"].'" class="img" width="30" height="30">': '');
			
			echo '<span>
					<a href="'.Sys::lml_get_url("user", $data["postyles"][0]["user_id"]).'&lang='.$lang.'" class="link_blue lpost_usr">
						'.$data["postyles"][0]["user_name"]." ".$data["postyles"][0]["user_last_name"].'</a>: 
					'.$data["postyles"][0]["description"].'
				</span>
				<div class="clear"></div>
			</div>';
			
			//Colocamos los items del postyle con el control mini_items
			$mini_item = new MiniItemControl();
			
			$params=array();
			$params['user_id']	= $user_id; //token
			$mini_item->ini(array(
				"params"		=> $params,
				"items"			=> $data["postyles"][0]["tags"],
				"width_control"	=> 530
			));
			$mini_item->printHtml(true);
			
			echo '<div class="clear"></div>';
			
			
			//comentarios del postyle
			$comments = new CommentBoxControl();
			
			$params["lang"]	=				$lang;
			$params["consumer_key"]	=		$consumer_key;
			$params["oauth_token"]	=		$oauth_token;
			$params["result_items_per_page"]= '15';
			$params["result_page"]			= '1';
			$params["postyle_id"] = $data["postyles"][0]['id'];
			$params["token_user_id"] = $user_id;
			$params["can_be_deleted"] = 0; //$data["postyles"][0]["can_be_deleted"];
			$data_commen = $this->postyle_model->get_comments($params);
			
			$con_data = array(
				"titulo" 			=> "",
				"comments"			=> $data_commen,
				"id" 				=> "ui_comments",
				"class" 			=> "comment",
				"link_all_comment"	=> true,
				"action_method"		=> 'postyle',
				"content_parent"	=> '#postyle_conte_detail',
				"params"			=> $params,
				"width_box"			=> 664,
				"is_of_postyle"		=> true
			);
			$comments->ini($con_data);
			$comments->printHtml(true);
		?>
			<input type="hidden" id="postyle_id" value="<?php echo $data["postyles"][0]['id'];?>">
			<input type="hidden" id="postyle_user_id" value="<?php echo $data["postyles"][0]['user_id'];?>">
		</div>
	</div>
	
	<div class="col_right">
		<?php
			//Control Ads Control 
			$this->load->view("ui_controls/pag_ads");
		?>
	</div>
	
	<div class="clear"></div>
</div>
