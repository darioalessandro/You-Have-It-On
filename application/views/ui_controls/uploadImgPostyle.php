<html>
<head>
<title>Upload file</title>
<link href="<?php echo base_url();?>application/css/style_upload_img.css" type="text/css" rel="stylesheet" media="screen">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url();?>application/scripts/lml_upload.js"></script>
</head>
<body>

<?php
	$html = '';
	$ver = 'none';
	if(is_array($error)){
		$html = '
		<input type="hidden" id="file_url" value="'.$error['url_file'].'">
		<input type="hidden" id="file_path" value="'.$error['file_path'].'">
		<input type="hidden" id="file_width" value="'.$error['file_width'].'">
		<input type="hidden" id="file_height" value="'.$error['file_height'].'">';
	}else{
		if($error!=''){
			$html = '<input type="hidden" id="error_ms" value="aa">';
			$ver = 'block';
		}
		$html .= $error;
	}
?>
<div id="msg" style="display:<?php echo $ver; ?>;">
<?php echo $html; ?>
</div>

<?php echo form_open_multipart('home/load_image', array(
	"id" => "frm_upload"
));?>

	<label for="upload_img"><?php echo lang("upload_photo")?>:</label> 
	<input type="file" id="upload_img" name="images[]" size="20" /> 
	<input type="submit" id="btn_upload" class="btn_posty" name="upload" value="<?php echo lang("upload"); ?>" /> <br />
	<?php echo lang("msg_upload_img")?>
</form>

</body>
</html>