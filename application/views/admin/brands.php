<html>
<head>
<title>Mini panel</title>
<style type="text/css">
#frame{
	position: absolute;
	width:450px;
	height:250px;
	top:15%;
	left:40%;
	display:none;
}
</style>
<script type="text/javascript">
function openFrame(name, id){
	var fra = document.getElementById('frame');
	fra.setAttribute("src", "<?php echo base_url().'adminqwy/load_imageBrand?id='; ?>"+id+"&name="+name);
	fra.style.display = 'block';
}
</script>
</head>
<body>
<a href="<?php echo base_url(); ?>adminqwy/verMenu">Menu</a>
<?php 
$params = array(
	'result_items_per_page' => '20',
	'result_page' => (isset($_GET['per_page'])? ($_GET['per_page']/20): '1')
);
$query = Sys::pagination("SELECT * FROM brands ORDER BY name", $params, true);
$res = $this->db->query($query['query']);

?>
<table>
	<tr>
		<td>Nombre</td>
	</tr>
	<?php foreach($res->result_array() as $row){?>
	<tr>
		<td><?php echo $row['name'];?></td>
		<td><a href="javascript:openFrame('<?php echo Sys::quitComillas($row['name']);?>', <?php echo $row['id']; ?>);">Imgs</a></td>
	</tr>
	<?php }?>
</table>

<?php
	$config['base_url'] = base_url().'adminqwy/brands?p=s';
	$config['total_rows'] = $query['total_rows'];
	$config['per_page'] = $params['result_items_per_page']; 
	$config['page_query_string'] = TRUE;
	
	$this->pagination->initialize($config); 
	
	echo $this->pagination->create_links();
?>

<iframe id="frame" src="">asd</iframe>
</body>
</html>