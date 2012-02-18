<html>
<head>
<title>Upload Form</title>
</head>
<body>


<?php echo form_open_multipart('services/postyle/add.json?consumer_key=89c1cd99a5ed1f0362668d15c15cb58104d7675fc&oauth_token=a16029b067f18a0900871779e3a314fc04d76773c');?>

description: <input type="text" name="description" /> <br>
ranking_id: <input type="text" name="ranking_id" /> <br>
ocation_id: <input type="text" name="ocation_id" /> <br>
lat: <input type="text" name="location_lat" /> <br>
lon: <input type="text" name="location_lon" /> <br> <br>

items_id: <input type="text" name="items_id[]" />, tag_description: <input type="text" name="tag_description[]" />, 
tag_width: <input type="text" name="tag_width[]" />, tag_height: <input type="text" name="tag_height[]" />,
tag_x: <input type="text" name="tag_x[]" />, tag_y: <input type="text" name="tag_y[]" /> <br>

items_id: <input type="text" name="items_id[]" />, tag_description: <input type="text" name="tag_description[]" />, 
tag_width: <input type="text" name="tag_width[]" />, tag_height: <input type="text" name="tag_height[]" />,
tag_x: <input type="text" name="tag_x[]" />, tag_y: <input type="text" name="tag_y[]" /> <br>

 <br> <br>

id: <input type="text" name="postyle_id" /> <br> <br>
image_url : <input type="text" name="image_url">

file1:
<input type="file" name="images[]" size="20" /> <br>
file2:
<input type="file" name="images[]" size="20" /> <br>
file3:
<input type="file" name="images[]" size="20" />

<br /><br />

<input type="submit" name="enviar" value="upload" />

</form>

</body>
</html>