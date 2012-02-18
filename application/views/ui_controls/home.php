<div id="home" class="margin_top">
	<div class="col_left margin_right">
		<?php
			include APPPATH.'/views/ui_controls/bar_left_base.php';
			
			//Insertamos el control de amigos
			$friendcontro = new FriendListControl();
			
			$params["result_items_per_page"] = '8';
			$params["result_page"] = '1';
			$params["user_id"]	= isset($params["user_id"])? $params["user_id"]: $params['token_user_id'];
			
			$data = $this->user_model->getFriendsUser($params);
			$data = array(
				"titulo" 		=> lang("friends"),
				"items" 		=> $data["friends"],
				"total_items" 	=> $data["total_rows"],
				"params" 		=> $params,
				"view_actionbar" => false,
				"num_cols"		=> 1
			);
			
			$friendcontro->ini($data);
			$friendcontro->printHtml(true); 
		?>
	</div>
	
	<div class="col_content margin_right">
	<?php if($params["user_id"] == $params['token_user_id']){ ?>
		<form class="frmpostyle" method="post" action="<?php echo base_url();?>new_postyle?lang=<?php echo $lang; ?>">
			<label for="msgPostyle"><?php echo lang("whats_your_style"); ?></label><input type="text" name="msg" id="msgPostyle">
			<input type="submit" name="submit" value="<?php echo lang("create_postyle"); ?>" class="btn_posty">
		</form>
	<?php }?>	
		<div id="postyle_conte" class="margin_top">
	<?php
		//Control Postyle se insertan los postyle 
		$params["result_items_per_page"]= '15';
		$params["result_page"]			= '1';
		$params["filter_user_id"]		= $params["user_id"];  //user del token o el de lmlid
		$params['user_id']				= $params['token_user_id'];
		$params['filter_order']			= "desc";
		if(isset($filter_both))
			$params['filter_both']		= $filter_both;
		else
			$params['filter_both']		= '0';
		
		$data = $this->postyle_model->getPostyles($params);
		$total_rows = $data["total_rows"];
		if($total_rows > 0){
			$postylecontro = new PostyleControl();
			$data = array(
				"titulo" 	=> "",
				"items" 	=> $data["postyles"],
				"params" 	=> $params,
				"width_commentbox"	=> 590
			);
			$postylecontro->ini($data);
			$postylecontro->printHtml(true);
		}else 
			echo '<div class="error_msg">'.lang("no_postyles").'
			</div>';
	
	?>
			<input type="hidden" id="per_page" value="<?php echo $params["result_items_per_page"]; ?>">
			<input type="hidden" id="filter_user_id" value="<?php echo $params["filter_user_id"]; ?>">
			<input type="hidden" id="filter_order" value="<?php echo $params["filter_order"]; ?>">
			<input type="hidden" id="filter_both" value="<?php echo $params["filter_both"]; ?>">
			<input type="hidden" id="me_id" value="<?php echo $params['token_user_id']; ?>">
		</div>
		<div class="btnsee_more">
			<?php
			 if($total_rows > $params["result_items_per_page"]){
			?>
			<div class="btn_sty postyle_more_result" insert="#postyle_conte" 
				page="<?php echo $params["result_page"]+1; ?>"><?php echo lang("view_more")?></div>
			<?php 
			 }?>
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
