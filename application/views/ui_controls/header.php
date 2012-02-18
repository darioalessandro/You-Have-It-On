<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $title;?></title>
<link href="<?php echo base_url();?>application/css/mimify-all_v1.1.css" type="text/css" rel="stylesheet" media="screen">
<link href="<?php echo base_url();?>application/css/mimify-muro_v1.1.css" type="text/css" rel="stylesheet" media="screen">
<link rel="shortcut icon" href="<?php echo base_url();?>application/images/favicon.ico" type="image/x-icon" />
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url();?>application/scripts/mimify-all_v1.1.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>application/scripts/mimify-muro_v1.1.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>application/scripts/<?php echo $lang; ?>_v1.1.js"></script>
<script type="text/javascript">
	$$.set_config({
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
		<a href="<?php echo base_url(); ?>?lang=<?php echo $lang; ?>" class="logo_lin"><img class="img_logo" alt="youhaveit" src="<?php echo base_url();?>application/images/youhaveit_home.png"></a>
		<img class="beta" alt="beta" src="<?php echo base_url();?>application/images/beta.png" width="32" height="32">
		
		<div class="notificaciones <?php echo $notifi['class']; ?>">
			<span class="notifi_num <?php echo $notifi['class']; ?>"><?php echo $notifi['num_notifica']; ?></span>
			<div id="show_notifys"></div>			
		</div>
		<div class="hed_menu">
			<ul>
				<li class="prim"><a href="<?php echo base_url();?>?lang=<?php echo $lang; ?>" style="width:35px;"><?php echo lang("home");?></a></li>
				<li><a href="<?php echo base_url();?>my_postyles?lang=<?php echo $lang; ?>" style="width:55px;"><?php echo lang("your_profile");?></a></li>
				<li><a href="mailto:contact@youhaveiton.com" style="width:55px;"><?php echo lang("contact");?></a></li>
				<li class="ulti"><a href="<?php echo base_url();?>services/user/logout?lang=<?php echo $lang; ?>"><?php echo lang("sign_out");?></a></li>
				<!-- <li><a href="" title="">Marcas</a></li>
				<li><a href="" title="">Invita amigos</a></li>
				<li><a href="" title="">Tu cuenta</a></li> -->
			</ul>
		</div>
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
