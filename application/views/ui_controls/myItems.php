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
		//Insertamos el Control ItemsList
		/*$res_itm = $this->db->query("SELECT c.id, c.name FROM category AS c INNER JOIN cat_attr AS ca ON c.id = ca.cat_id 
			INNER JOIN item_cat_attr_val AS icav ON ca.id = icav.cat_attr_id INNER JOIN items_users AS iu ON icav.item_id = iu.item_id 
			WHERE iu.user_id = ".($_GET["lmlid"]>0? $_GET["lmlid"]: $token_user_id)." AND c.category_parent IS NULL GROUP BY c.id LIMIT 1");
		if($res_itm->num_rows() > 0){
			$data_itm = $res_itm->row();*/
			$itemlistcontro = new ItemListControl();
			
			$params["result_items_per_page"] = '18';
			$params["result_page"] = '1';
			$params["filter_category_id"] = '1';//$data_itm->id;
			$params["only_my_items"] = '1';
			$params['filter_user_id'] = $params["user_id"];
			$data = $this->item_model->getItems($params);
			//if($data["total_rows"] > 0){
				$data = array(
					"titulo" 	=> "Cat mart",
					"items" 	=> $data["items"],
					"categorys"	=> $data["category"],
					"total_items" 	=> $data["total_rows"],
					"params" 		=> $params,
					"view_actionbar" => false,
					"num_cols"		=> 3,
					"cat_per_page"	=> 6,
					"detailed_item"	=> false,
					"open_detail_win_click"	=> false
				);
				$itemlistcontro->ini($data);
				$itemlistcontro->printHtml(true);
			/*}else 
			echo '<div class="error_msg">'.lang("no_items").'
			</div>';*/
		
	
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
