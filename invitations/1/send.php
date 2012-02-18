<?php
// Varios destinatarios
$para  = 'darioalessandrolencina@gmail.com,'; // atención a la coma
$para .= 'arlm01@gmail.com,';
$para .= 'lazio2500@hotmail.com,';
$para .= 'gamalielmendozasolis@gmail.com';

// subject
$titulo = 'Invitación YouHaveItOn';

// message
$mensaje = '
		<html>
	<head>
		<title>YouHaveItOn</title>
	</head>
	<body style="margin:0;">
		<table border="0" cellspacing="0" cellpadding="0"  align="center">
			<tr>
				<td height="174"><a  href="http://www.youhaveiton.com"><img border="0" style="display:block;" src="http://www.youhaveiton.com/invitations/1/1.jpg" alt="background" /></a></td>
			</tr>
			<tr>
				<td height="505"><a  href="http://www.youhaveiton.com"><img border="0" style="display:block;" src="http://www.youhaveiton.com/invitations/1/2.jpg" alt="background" /></a></td>
			</tr>
			<tr>
				<td height="405"><a  href="http://www.youhaveiton.com"><img border="0" style="display:block;" src="http://www.youhaveiton.com/invitations/1/3.jpg" alt="background" /></a></td>
			</tr>
			<tr>
				<td height="658"><a  href="http://www.youhaveiton.com"><img border="0" style="display:block;" src="http://www.youhaveiton.com/invitations/1/4.jpg" alt="background" /></a></td>
			</tr>
			<tr>
				<td height="199"><a  href="http://www.youhaveiton.com/services/facebook/sigin?lang=eng"><img style="display:block;" border="0" src="http://www.youhaveiton.com/invitations/1/5.jpg" alt="background" /></a></td>
			</tr>
		</table>
	</body>
</html>
';

// Para enviar un correo HTML mail, la cabecera Content-type debe fijarse
$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

$cabeceras .= 'From: YouHaveItOn <notify@youhaveiton.com>' . "\r\n";

// Mail it
mail($para, $titulo, $mensaje, $cabeceras);
?>