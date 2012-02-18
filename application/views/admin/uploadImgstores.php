<html>
<head>
<title>Upload file</title>
<link href="<?php echo base_url();?>application/css/style_upload_img.css" type="text/css" rel="stylesheet" media="screen">
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
		if($error=='aa'){
			$ver = 'block';
			$html = 'Se agregaron correctamente.';
		}else
			$html = $error;
	}
?>
<h3><?php echo $_GET['name'];?></h3>
<div id="msg" style="display:<?php echo $ver; ?>;">
<?php echo $html; ?>
</div>

<?php echo form_open_multipart('adminqwy/load_imagestores?id='.$_GET['id']."&name=".$_GET['name'], array(
	"id" => "frm_upload"
));?>

	<label for="upload_img">Imagenes:</label> <br>
	<input type="text" id="upload_img" name="imag[]" size="40" /> <br>
	<input type="text" id="upload_img" name="imag[]" size="40" /> <br>
	<input type="text" id="upload_img" name="imag[]" size="40" /> <br>
	<input type="submit" id="btn_upload" class="btn_posty" name="upload" value="Enviar" /> <br />
</form>

</body>
</html>