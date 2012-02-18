<div id="home" class="margin_top">
	<div class="col_content dtlpostyle margin_right">
		<div id="postyle_conte_detail" class="new_postyle">
			<a href="javascript:void(0);" class="new_postyle_clear"><?php echo lang("new_postyle")?></a>
			<div class="clear"></div>
			<iframe class="ifr_img_postyle" src="<?php echo base_url()?>load_image?lang=<?php echo $lang; ?>"><?php echo lang("upload_image");?></iframe>
			<div class="upload_pst_loader">
				<label><?php echo lang("loading_image");?>...</label><br>
				<img src="<?php echo base_url(); ?>application/images/loader.gif"> <br>
				<?php echo lang("msg_upload_img2")?></div>
			
			<div class="img_view_tag">
				<img id="add_img_postyle" alt="image" src="<?php echo base_url(); ?>application/images/loader.gif">
			</div>
			
			<?php echo form_open_multipart('new_postyle', array(
				"class" => "frm_postyle_publish", "id" => "frmSavePostyle"
			));
			//new_postyle
			//services/postyle/add.json?consumer_key='.$consumer_key.'&oauth_token='.$oauth_token.'
			?>
				<input type="hidden" id="img_upload_file" name="image_url">
				<input type="hidden" id="x1" value="">
				<input type="hidden" id="y1" value="">
				<input type="hidden" id="w" value="">
				<input type="hidden" id="h" value="">
				<p>
					<label><?php echo lang("whats_your_style"); 
					if(isset($_POST["msg"])){
						if($_POST["msg"]==lang('whats_your_style'))
							$_POST["msg"] = '';
					}else
						$_POST["msg"] = '';
					?></label><br>
					<textarea class="expand50-200" name="description" rows="2" cols="60"><?php echo $_POST["msg"];?></textarea><br>
					<?php echo lang("msg_posty_descrip"); ?>
				</p>
				
				<label><?php echo lang("ocation"); ?></label><br>
				<input type="text" id="ocation_aucomplete1" name="ocation_label" class="txt_ocation" autocomplete="off">
				<div id="ocation_aucomplete11" class="auto_result">
					<input type="hidden" id="autocation_id" class="pst_ponid" name="ocation_id">
				</div>
				<?php echo lang("msg_posty_ocation"); ?>
				
				<p>
					<label>Items</label>
				</p>
				<div id="items_added">
				</div>
				<div class="clear"></div>
				<span id="preloader_postyle"></span>
				<input type="submit" name="save" value="<?php echo lang("publish"); ?>" class="btn_publish">
				<div class="clear"></div>
			</form>
		</div>
		<input type="hidden" name="me_id" id="me_id" value="<?php echo $token_user_id; ?>">
	</div>
	
	<div class="col_right">
		<?php
			//Control Ads Control 
			$this->load->view("ui_controls/pag_ads");
		?>
	</div>
	
	<div class="clear"></div>
</div>


<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">window.onload = function(){initialize(); };</script>
<div id="conte_mapa">
	<div class="title"><?php echo lang("location"); ?></div>
	<div id="wrap_mapa"></div>
	<input type="button" id="btn_selLocation" value="<?php echo lang("select_location"); ?>" class="btn_publish">
	<div class="clear"></div>
</div>

