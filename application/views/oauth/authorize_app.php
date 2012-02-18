<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo lang('txt_title_authorize_app'); ?> </title>

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
		<?php echo form_open('services/oauth/server?action=authorize'.$uri); ?>
		
		<p>Acepta que la aplicacion "<?php echo $osr_application_title; ?>" acceda a sus datos.</p>
		<input type="submit" name="authorized" value="<?php echo lang('txt_oallow'); ?>" />
		<input type="submit" name="authorized" value="<?php echo lang('txt_odeny'); ?>" />
		
		</form>
	</div>
</body>
</html>