<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo lang('txt_title_user_logout'); ?> </title>

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
		<div class="button">
			<a href="<?php echo base_url()."services/user/logout?lang=".$lang; ?>" title="<?php echo lang('txt_user_logout'); ?>">
				<?php echo lang('txt_user_logout'); ?></a>
		</div>
	</div>
</body>
</html>