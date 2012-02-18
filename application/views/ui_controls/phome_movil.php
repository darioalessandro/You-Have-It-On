<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $title;?></title>
<link href="<?php echo base_url();?>application/css/style-home-movil.css" type="text/css" rel="stylesheet" media="screen">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url();?>application/scripts/md5.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>application/scripts/js_lml.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>application/scripts/lml_home.js"></script>
<script type="text/javascript">
	$$.set_config({
		cssct:			"<?php echo $consumer_secret;?>",
		url_base:		"<?php echo base_url();?>",
		consumer_key:	"<?php echo $consumer_key;?>",
		oauth_token:	"<?php echo $oauth_token;?>",
		lang:			"<?php echo $lang; ?>"
	});
</script>
</head>
<body>
<div id="home_movil">
	<div class="logo"><img alt="youhaveit" class="logo" src="<?php echo base_url();?>application/images/logo_big.png"></div>
	
	<form action="<?php echo base_url()?>services/user/login?lang=<?php echo $lang; ?>" method="post" accept-charset="utf-8">
		<label for="log_email"><?php echo lang("email"); ?></label> <br>
		<input type="email" id="log_email" name="email"> <br>
		<label for="log_password"><?php echo lang("password"); ?></label> <br>
		<input type="password" id="log_password" name="password"> <br>
		<a href="<?php echo base_url(); ?>?lang=<?php echo $lang; ?>" class="link_log"><?php echo lang("forgot_password"); ?></a> <br>
		<input type="submit" name="login" value="<?php echo lang("access"); ?>" id="btn_login">
		<div class="clear"></div>
		<a href="<?php echo base_url(); ?>services/facebook/sigin?lang=<?php echo $lang;?>" class="log_with"><?php echo lang("txt_login_with_facebook"); ?></a>
		&ensp; <span>&bull;</span> &ensp;
		<a href="<?php echo base_url(); ?>services/google/sigin?lang=<?php echo $lang;?>&go" class="log_with"><?php echo lang("txt_login_with_google"); ?></a>
	</form>
</div>
<a href="?lang=<?php echo $lang; ?>" class="btn_registro"><?php echo lang("join"); ?></a>
</body>
</html>