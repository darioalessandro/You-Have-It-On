<div id="home" class="margin_top">
	<div class="col_left margin_right">
	<?php
	include APPPATH.'/views/ui_controls/bar_left_base.php';
	?>
	</div>
	
	<div class="col_content margin_right">
	<?php 
		$params["user_id"]	= isset($params["user_id"])? $params["user_id"]: $params['token_user_id']; //user id para el friendscontrol
		
		if($params["user_id"] == $params['token_user_id']){ ?>
		<form class="frmpostyle" method="post" action="<?php echo base_url();?>new_postyle?lang=<?php echo $lang; ?>">
			<label for="msgPostyle"><?php echo lang("whats_your_style"); ?></label><input type="text" name="msg" id="msgPostyle">
			<input type="submit" name="submit" value="<?php echo lang("create_postyle"); ?>" class="btn_posty">
		</form>
	<?php }?>	
		<div id="hfriends_conte" class="margin_top">
		<?php
		//Insertamos el control de amigos
			$params["result_items_per_page"] = '18';
			$params["result_page"] = '1';
			
			$data = $this->user_model->getFriendsUser($params);
			if(count($data["friends"]) > 0){
				$friendcontro = new FriendListControl();
				$data = array(
					"titulo" 		=> "",
					"items" 		=> $data["friends"],
					"total_items" 	=> $data["total_rows"],
					"params" 		=> $params,
					"view_actionbar" => true,
					"num_cols"		=> 3,
					"show_link_more" => false
				);
				$friendcontro->ini($data);
				$friendcontro->printHtml(true); 
			}else 
				echo '<div class="error_msg">'.lang("no_friends").'
			</div>';
		?>
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
