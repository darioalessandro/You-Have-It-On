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
		<h5><?php echo lang('txt_app_id'); ?>: <?php echo $id; ?></h5>
		<h5><?php echo lang('txt_app_api_key'); ?>: <?php echo $consumer_key; ?></h5>
		<h5><?php echo lang('txt_app_secret'); ?>: <?php echo $consumer_secret; ?></h5>
	</div>
</body>
</html>