<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $title;?></title>
<link href="<?php echo base_url();?>application/css/mimify-all_v1.1.css" type="text/css" rel="stylesheet" media="screen">
<link href="<?php echo base_url();?>application/css/mimify-home_v1.1.css" type="text/css" rel="stylesheet" media="screen">
<link rel="shortcut icon" href="<?php echo base_url();?>application/images/favicon.ico" type="image/x-icon" />
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url();?>application/scripts/mimify-all_v1.1.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>application/scripts/mimify-home_v1.1.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>application/scripts/<?php echo $lang; ?>_v1.1.js"></script>
<script type="text/javascript">
	$$.set_config({
		cssct:			"<?php echo $consumer_secret;?>",
		url_base:		"<?php echo base_url();?>",
		consumer_key:	"<?php echo $consumer_key;?>",
		oauth_token:	"<?php echo $oauth_token;?>",
		lang:			"<?php echo $lang; ?>"
	});
</script>
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-4256827-6']);
  _gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
</head>
<body>
<div class="header">
	<div class="hed_conten">
		<a href="<?php echo base_url(); ?>?lang=<?php echo $lang; ?>" class="img_logo"><img alt="youhaveit" src="<?php echo base_url();?>application/images/youhaveit_home.png"></a>
		<img class="beta" alt="beta" src="<?php echo base_url();?>application/images/beta.png" width="32" height="32">
		<!-- <div class="hed_login">
			<form action="<?php echo base_url()?>services/user/login?lang=<?php echo $lang; ?>" method="post" accept-charset="utf-8">
				<label for="log_email"><?php echo lang("email"); ?></label> <input type="text" id="log_email" name="email">
				<label for="log_password"><?php echo lang("password"); ?></label> <input type="password" id="log_password" name="password">
				<input type="submit" name="login" value="<?php echo lang("access"); ?>" id="btn_login"> <br>
				<label class="remember"><input type="checkbox" name="remember_me"> <?php echo lang("remember_me")?></label>
				<a href="<?php echo base_url(); ?>?lang=<?php echo $lang; ?>" class="link_log"><?php echo lang("forgot_password"); ?></a>
				<div class="clear"></div>
			</form>
			
			<div class="connect_with">
				<a href="<?php echo base_url(); ?>services/facebook/sigin?lang=<?php echo $lang;?>"><img src="<?php echo base_url(); ?>application/images/connect_facebook.png"></a>
				<a href="<?php echo base_url(); ?>services/google/sigin?lang=<?php echo $lang;?>&go"><img src="<?php echo base_url(); ?>application/images/connect_google.png"></a>
			</div> 
		</div> -->
		
		<div class="hed_busqueda">
			<label for="txtsearsh" id="hed_lblbuscar"><?php echo lang("msg_search"); ?></label> 
			<input type="text" name="txtsearsh" class="hed_txtbuscar" id="txtsearsh" autocomplete="off"> | 
			<input type="image" src="<?php echo base_url();?>application/images/hed_buscar.png">
			<span class="search_example"><?php echo lang("example_search"); ?></span>
			<div id="txtsearsh1" class="auto_result">
				
			</div>
		</div>
		
		<div class="clear"></div>
	</div>
</div>
