<div id="home">
	<div class="home_cnt">
		<div class="gallery_home">
			<div class="titless"><?php echo lang("msg_gallery");?></div>
			<div class="clear"></div>
			
			<div id="slider">
				<ul>
					<li>
						<img src="<?php echo base_url();?>application/images/postyle.png" alt="">
						<div class="inff imgff2">
							<div class="infftitle"><?php echo lang("msg_gallery_title1"); ?></div>
							<p><?php echo lang("msg1_gallery_img1"); ?></p>
							<p><?php echo lang("msg2_gallery_img1"); ?></p>
						</div>
					</li>
					<li>
						<img src="<?php echo base_url();?>application/images/items.png" alt="">
						<div class="inff imgff1">
							<div class="infftitle"><?php echo lang("msg_gallery_title2"); ?></div>
							<p><?php echo lang("msg1_gallery_img2"); ?></p>
							<p><?php echo lang("msg2_gallery_img2"); ?></p>
						</div>
					</li>
				</ul>
			</div>
		</div>
		
		<div class="register">
			<div class="rgtitle"><!-- <?php echo lang("txt_login_with_facebook")?> --></div>
			<span id="face_login">
				<a class="link_face" href="<?php echo base_url(); ?>services/facebook/sigin?lang=<?php echo $lang;?>"><?php echo lang("connect_with_facebook"); ?></a>
				<img src="<?php echo base_url(); ?>application/images/loader.gif" class="loader_linface" style="display:none;">
			</span>
			<!-- <form id="frmRegister" method="post">
				<div class="rgtitle"><?php echo lang("join")?></div>
				<label class="padding"><?php echo lang("name"); ?></label> <input type="text" id="rg_name" name="name"><br>
				<label class="padding"><?php echo lang("sexo"); ?></label>
					<select name="sex" id="rg_sex">
						<option value="m"><?php echo lang("man");?></option>
						<option value="f"><?php echo lang("women");?></option>
					</select><br>
				<label class="padding"><?php echo lang("email"); ?></label> <input type="text" id="rg_email" name="email"><br>
				<label class="padding"><?php echo lang("password"); ?></label> <input type="password" id="rg_password" name="password"><br>
				<label class="paddingm"><?php echo lang("password_conf"); ?></label> <input type="password" id="rg_password2" name="password2"><br>
				<div class="regis_msg"><?php echo lang("join_msg1"); ?> <a href="?lang=<?php echo $lang; ?>" class="link_blue"><?php echo lang("join_msg2"); ?></a> 
					<?php echo lang("join_msg3");?> <a href="?lang=<?php echo $lang; ?>" class="link_blue"><?php echo lang("join_msg4"); ?></a>
				</div>
				<div class="clear"></div>
				<span id="preloader_rg"></span>
				<input type="submit" name="access" value="<?php echo lang("access"); ?>" class="btn_tinto">
			</form> -->
			<div class="regis_msg"><?php echo lang("join_msg1"); ?> <a href="<?php echo base_url();?>info/terms?lang=<?php echo $lang; ?>" class="link_blue"><?php echo lang("join_msg2"); ?></a> 
				<?php echo lang("join_msg3");?> <a href="<?php echo base_url();?>info/terms?lang=<?php echo $lang; ?>" class="link_blue"><?php echo lang("join_msg4"); ?></a>
			</div>
		</div>
		
		<div class="clear"></div>
	</div>
</div>
