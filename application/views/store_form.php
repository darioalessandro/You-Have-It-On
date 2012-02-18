<html>
<head>
<title>Upload Form</title>
</head>
<body>


<?php echo form_open_multipart('services/store/edit.json?consumer_key=89c1cd99a5ed1f0362668d15c15cb58104d7675fc&oauth_token=a16029b067f18a0900871779e3a314fc04d76773c');?>

name: <input type="text" name="name" /> <br>
description: <input type="text" name="description" /> <br>
lat: <input type="text" name="lat" /> <br>
lon: <input type="text" name="lon" /> <br>
principal: <input type="text" name="principal" /> <br> <br>

store_id: <input type="text" name="store_id" /> <br>
street_name: <input type="text" name="location_street_name" /> <br>
city: <input type="text" name="location_city" /> <br>
state: <input type="text" name="location_state" /> <br>
contry: <input type="text" name="location_country" /> <br>
postal_code: <input type="text" name="location_postal_code" /> <br>
description: <input type="text" name="location_description" /> <br> <br>

web site: <input type="text" name="website[]" /> <br>

id: <input type="text" name="days_range_id[]" />, day1: <input type="text" name="day_id[]" />, open_hour1: <input type="text" name="open_hour[]" />, 
close_hour1: <input type="text" name="close_hour[]" /> <br>
id: <input type="text" name="days_range_id[]" />, day2: <input type="text" name="day_id[]" />, open_hour2: <input type="text" name="open_hour[]" />, 
close_hour2: <input type="text" name="close_hour[]" /> <br>
id: <input type="text" name="days_range_id[]" />, day3: <input type="text" name="day_id[]" />, open_hour3: <input type="text" name="open_hour[]" />, 
close_hour3: <input type="text" name="close_hour[]" /> <br> <br>

brand_id: <input type="text" name="brand_id" /> <br> <br>

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