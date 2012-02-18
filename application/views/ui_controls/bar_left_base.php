		<?php
			$params = array(
				"lang"=>					$lang,
				"consumer_key"=>			$consumer_key,
				"oauth_token"=>				$oauth_token
			);
		
			//Cueadro de la informacion del usuario
			if($_GET["lmlid"] > 0)
				$params["user_id"] = $_GET["lmlid"];
			$info = $this->user_model->getInfoUser($params);
			if(count($info) > 0){
		?>
		<div class="my_info">
		<?php 
		//datos del usuario de token
		$res_usr = $this->user_model->getIdUserToken($params);
		$data_usr = $res_usr->row_array();
		$params['token_user_id'] = $data_usr['id'];
			
		if(isset($params["user_id"])){ ?>
			<span class="name_usr"><?php echo $info["user"]["name"]." ".$info["user"]["last_name"]; ?></span>
		<?php
			//imagen del usuario 
			$width_img = 162;
			$width = $height = $width_img;
			if(isset($info["user"]["images"][0])){
				list($width, $height, $type, $attr) = getimagesize($info["user"]["images"][0]["sizes"][1]["url"]);
				
				$porcent	= $width_img*100/$width;
				$width		= $width_img;
				$height		= ceil($height*$porcent/100);
			}
				
				echo (count($info["user"]["images"])>0? 
				'<img src="'.$info["user"]["images"][0]["sizes"][1]["url"].'" width="'.$width.'" height="'.$height.'">': ''); 
				
				//ponemos el action bar para
				$actionbar = new ActionBarControl();
				$data["items"] = array();
				$is_friend = $this->user_model->isMyFriend($params, true);
				if($is_friend!=false){
					$clas='';
					if($is_friend->status==1)
						$text = lang("remove_friends");
					else{
						$text = lang("cancel_invitation");
						$clas = ' request';
					}
					$data["items"][] = array(
						"url"		=> "javascript:void(0);",
						"name"		=> $text,
						"action"	=> "remove_friend".$clas
					);
				}else{
					$data["items"][] = array(
						"url"		=> "javascript:void(0);",
						"name"		=> lang("add_friends"),
						"action"	=> "add_friend"
					);
				}
				
				echo '<div class="ui_friends">';
				$actionbar->ini($data);
				$actionbar->printHtml(true);
				echo '
					<input type="hidden" id="user_id" value="'.$params["user_id"].'">
					<input type="hidden" id="me_id" value="'.$params['token_user_id'].'">
				</div>';
		?>
		<?php }else{
			echo (count($info["user"]["images"])>0? 
				'<img src="'.$info["user"]["images"][0]["sizes"][3]["url"].'" width="45" height="45">': ''); ?>
			<span><?php echo $info["user"]["name"]." ".$info["user"]["last_name"]; ?></span>
		<?php }?>
			<div class="clear"></div>
		</div>
		<?php }?>
		
		<div class="menu_lhome margin_top">
			<?php
				$mis = '';
				$lmlid = '?lang='.$lang;
				if(!isset($params["user_id"])){
					$mis = lang("my")." "; 
				}else 
					$lmlid = "?lmlid=".$params["user_id"].'&lang='.$lang;
			?>
			<ul>
				<li class="liwall"><a href="<?php echo base_url().$lmlid;?>" class="link_blue"><?php echo lang("wall"); ?></a></li>
				<li class="lipostyle"><a href="<?php echo base_url();?>my_postyles<?php echo $lmlid; ?>" class="link_blue"><?php echo $mis."Postyles"; ?></a></li>
				<li class="liitem"><a href="<?php echo base_url();?>my_items<?php echo $lmlid; ?>" class="link_blue"><?php echo $mis."Items"; ?></a></li>
				<li class="lifriend"><a href="<?php echo base_url();?>my_friends<?php echo $lmlid; ?>" class="link_blue"><?php echo $mis.lang("friends"); ?></a></li>
				<!-- <li class="lirank"><a href="" class="link_blue"><?php echo $mis."Rankings"; ?></a></li>
				<li class="liaward"><a href="" class="link_blue"><?php echo $mis.lang("awards"); ?></a></li> -->
			</ul>
			<span class="menu_point" style="top:<?php echo $pos_menu; ?>px"></span>
			<div class="clear"></div>
		</div>
