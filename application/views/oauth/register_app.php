<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo lang('txt_title_register_app'); ?> </title>

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

		<?php echo form_open('services/oauth/registerApp'); ?>
		
		<h5>*<?php echo lang('txt_app_title'); ?></h5>
		<input type="text" name="application_title" value="<?php echo set_value('application_title'); ?>" size="50" />
		
		<h5><?php echo lang('txt_app_descr'); ?></h5>
		<input type="text" name="application_descr" value="<?php echo set_value('application_descr'); ?>" size="50" />
		
		<h5><?php echo lang('txt_app_notes'); ?></h5>
		<input type="text" name="application_notes" value="<?php echo set_value('application_notes'); ?>" size="50" />
		
		<h5>*<?php echo lang('txt_app_type'); ?></h5>
		<select name="application_type">
			<option value="" <?php echo set_select('application_type', ''); ?>>
				<?php echo lang('txt_app_type_select_opc'); ?>
			</option>
			<option value="website" <?php echo set_select('application_type', 'website'); ?>>
				<?php echo lang('txt_app_type_website'); ?>
			</option>
			<option value="appmobile" <?php echo set_select('application_type', 'appmobile'); ?>>
				<?php echo lang('txt_app_type_appmobile'); ?>
			</option>
		</select>
		
		<h5><?php echo lang('txt_application_uri_app'); ?></h5>
		<input type="text" name="application_uri" value="<?php echo set_value('application_uri'); ?>" size="50" />
		
		
		<div><input type="submit" name="register" value="<?php echo lang('txt_register_app'); ?>" /></div>
		
		</form>
	</div>
</body>
</html>