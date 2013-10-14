<?php session_start(); 
mysql_connect('localhost','USER','PASSWORD') or die ('Ha fallado la conexi&oacute;n:'.mysql_error());
mysql_select_db('DATABASE') or die ('Error al seleccionar la base de datos.'.mysql_error());

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="formato.css" />
<title>SGConquers - Principal</title>
</head>

<body>
<div id="background">

<div id="fondo">


<div id="contenedor">
<div id="recursos">
<form action="index.php" method="post">
<table>
<tr>
<td>Usuario:<input type="text" name="nick" /></td>
<td>Contraseña:<input type="password" name="contraseña" /></td>
<td><input type="submit" name="enviar" value="Enviar" /></td>
<td></form>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="PNBBMUX9P422N">
<input type="image" src="https://www.paypalobjects.com/es_ES/ES/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal. La forma rápida y segura de pagar en Internet.">
<img alt="" border="0" src="https://www.paypalobjects.com/es_ES/i/scr/pixel.gif" width="1" height="1">
</form>
</td>
</tr>
</table>

</div>
<center><div id="anuncioban">
<p>Hueco para anuncio</p>
</div></center>

</div>

<div id="contenedor">

<div id="lateral">
<p>Enlaces:</p>
<p><a href="http://foro.sgconquers.hol.es">Foro</a></p>
<p><a href="registro.php">Regístrate</a></p>
<p><a href="bugs.alphasgcon.hol.es">Bugs</a></p>
</div>

<div id="principal">

<p>SgConquers es un juego hecho por aficionados de la serie stargate, basado en dicha serie y sin ánimo de lucro. Actualmente se encuentra en una fase muy temprana de desarrollo y aún no es jugable, rogamos paciencia, en cuanto podamos publicaremos una beta pública. Actualmente no dispongo de mucho tiempo por lo que el desarrollo del juego se verá ralentizado y no se cuando verá la luz</p>
<div id="anunciorob">
<p>Hueco para anuncios</p>
</div>
</div>


<div id="pie">
SGconquers es un proyecto opensource y sin &aacute;nimo de lucro, cualquiera que desee ayudar es bienvenid@.
</div>


</div>


</div>
</div>
<p>&nbsp;</p>
</body>
</html>

<?php

function codigoseguro(){
	$caracteres='abcdefghijklmnopqrstuvxyzwABCDEFGHIJKLMNOPQRSTUVXYZW123456789';
	mt_srand (time());
	$codigo = "";
	for ($i=1;$i<=32;$i++){
		$random = mt_rand(0,60);
		$codigo = $codigo . $caracteres[$random];
	}
	return $codigo;
}

if (isset($_POST["enviar"])) {
	if(trim($_POST["nick"]) != "" && trim($_POST["contraseña"]) != "") {
		$nick = htmlentities($_POST["nick"]);
		$contraseña = htmlentities($_POST["contraseña"]);
		$contraseña = md5($contraseña);
		
		$result = mysql_query('SELECT id, password, nick, activado, raza FROM jugadores WHERE nick =\''.$nick.'\'');
		if($row = mysql_fetch_array($result)){
			if($row["password"] == $contraseña){
				if($row["activado"] != 0){
					$_SESSION["nick"] = $row['nick'];
					$_SESSION["identificador"] = $row['id'];
					$_SESSION["raza"] = $row['raza'];
					$codigotemporal = codigoseguro();
					$_SESSION["codigoseguridad"] = $codigotemporal;
					mysql_query('UPDATE jugadores SET codigoseguridad=\''.$codigotemporal.'\' WHERE id =\''.$row['id'].'\' ');
		
					echo '<SCRIPT LANGUAJE="javascript">';
					echo 'location.href = "juego-index.php";';
					echo '</SCRIPT>';
				}else{
					echo '<script type="text/javascript">alert("El usuario no ha sido activado a&uacute;n");</script>';
				}
		
        	}else{
				echo '<script type="text/javascript">alert("password incorrecto: '.$contraseña.'");</script>';
			}
		}else{
			echo '<script type="text/javascript">alert("Usuario no existente en la base de datos");</script>';
		}
	}
}	

?>