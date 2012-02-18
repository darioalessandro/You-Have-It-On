<html>
<head>
<title>Test js lml</title>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>application/scripts/js_lml.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$("#ejecutar").click(function(){
		eval('$$.'+$("#metodo").val()+'('+
				$("#text").val()+');');
	});
});
</script>
</head>
<body>
<form>
	<label><strong>Nombre del metodo:</strong></label><br>
	<input type="text" id="metodo" value="config_query"><br>
	
	<label><strong>Parametros (json):</strong></label><br>
	<textarea id="text" rows="10" cols="80">{
	lang:Language.SPA,
	consumer_key:'89c1cd99a5ed1f0362668d15c15cb58104d7675fc',
	callback: function(data){
		$("#aqui").html($$.dump(data));
	}
}</textarea>
	<input type="button" value="Ejecutar" id="ejecutar">
</form>

<h2>Resultado</h2>
<pre id="aqui">
</pre>

</body>
</html>