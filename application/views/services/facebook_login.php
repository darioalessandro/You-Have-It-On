<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $title_page; ?></title>

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
	<div id="fb-root"></div>
      <script src="http://connect.facebook.net/en_US/all.js"></script>
      <script>
	      FB.init({appId: '<?php echo $app_id; ?>', status: true,
	               cookie: true, xfbml: true});
	      FB.Event.subscribe('auth.login', function(response) {
	        window.location.reload();
	      });
	  </script>
	  <?php if(is_array($data)) { ?>
      	Welcome <?= $user->name ?>
      <?php } else { ?>
      	<fb:login-button><?php echo $msg_buttom; ?></fb:login-button>
      <?php } ?>
      
</body>
</html>