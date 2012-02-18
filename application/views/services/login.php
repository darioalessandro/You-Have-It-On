<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo lang('txt_title_user_login'); ?> </title>

<style type="text/css">

body {
 background-color: #fff;
 margin:0;
 color: #4F5155;
}

a {
 color: #003399;
 background-color: transparent;
 font-weight: normal;
}

</style>
</head>

<body>
	<div>
		<?php echo nl2br(validation_errors()); ?>

		<?php echo form_open('services/user/login'); ?>
		
		<h5>*<?php echo lang('txt_login_email'); ?></h5>
		<input type="text" name="email" value="<?php echo set_value('email'); ?>" size="50" />
		
		<h5>*<?php echo lang('txt_login_password'); ?></h5>
		<input type="password" name="password" value="<?php echo set_value('password'); ?>" size="50" />
		
		<input type="submit" name="login" value="<?php echo lang('txt_login_submit'); ?>" />
		
		</form>
		
		<div class="button">
			<a href="<?php echo base_url()."services/google/sigin?lang=".$lang."&go"; ?>" title="<?php echo lang('txt_login_with_google'); ?>">
				<?php echo lang('txt_login_with_google'); ?></a>
		</div>
		
		<div class="button">
			<a href="<?php echo base_url()."services/facebook/sigin?lang=".$lang; ?>" title="<?php echo lang('txt_login_with_facebook'); ?>">
				<?php echo lang('txt_login_with_facebook'); ?></a>
		</div>
		
		<div class="button">
			<a href="<?php echo base_url()."services/twitter/sigin?lang=".$lang; ?>" title="<?php echo lang('txt_join_twitter'); ?>">
				<?php echo lang('txt_join_twitter'); ?></a>
		</div>
	</div>
</body>
</html>