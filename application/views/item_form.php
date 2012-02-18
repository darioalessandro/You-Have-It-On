<html>
<head>
<title>Upload Form</title>
</head>
<body>


<?php
//oauth_token=ce9c7b8a5d21e115681648a1c0dd562004d9b5686&consumer_key=89c1cd99a5ed1f0362668d15c15cb58104d7675fc
echo form_open_multipart('services/item/add.json?consumer_key=89c1cd99a5ed1f0362668d15c15cb58104d7675fc&oauth_token=a16029b067f18a0900871779e3a314fc04d76773c');?>

label: <input type="text" name="label" /> <br>
brand_id: <input type="text" name="brand_id" /> <br>
bought_in: <input type="text" name="bought_in" /> <br>
price: <input type="text" name="price" /> <br> <br>

cat_attr_id1: <input type="text" name="cat_attr_id[]" />, value1: <input type="text" name="value[]" /> <br>
cat_attr_id2: <input type="text" name="cat_attr_id[]" />, value2: <input type="text" name="value[]" /> <br>
cat_attr_id3: <input type="text" name="cat_attr_id[]" />, value3: <input type="text" name="value[]" /> <br> <br>

id: <input type="text" name="item_id" /> <br> <br>

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