<html>
<head>
<title>Upload Form</title>
</head>
<body>


<?php echo form_open_multipart('services/brand/edit.json?consumer_key=89c1cd99a5ed1f0362668d15c15cb58104d7675fc&oauth_token=a16029b067f18a0900871779e3a314fc04d76773c');?>

name: <input type="text" name="name" /> <br>
description: <input type="text" name="description" /> <br>
country: <input type="text" name="country" /> <br>
id: <input type="text" name="brand_id" /> <br><br>

lat: <input type="text" name="lat" /> <br>
lon: <input type="text" name="lon" /> <br>
street_name: <input type="text" name="location_street_name" /> <br>
city: <input type="text" name="location_city" /> <br>
state: <input type="text" name="location_state" /> <br>
contry: <input type="text" name="location_country" /> <br>
postal_code: <input type="text" name="location_postal_code" /> <br>
description: <input type="text" name="location_description" /> <br> <br>

web site: <input type="text" name="website[]" /> <br>
web site: <input type="text" name="website[]" /> <br> <br>

phone_number: <input type="text" name="phone_number[]" /> <br>
phone_number: <input type="text" name="phone_number[]" /> <br> <br>

brand_categories: <input type="text" name="brand_categories[]" /> <br>
brand_categories: <input type="text" name="brand_categories[]" /> <br> <br>

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