<?php session_start(); 
mysql_connect('localhost','USER','PASSWORD') or die ('Ha fallado la conexi&oacute;n:'.mysql_error());
mysql_select_db('DATABASE') or die ('Error al seleccionar la base de datos.'.mysql_error());

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<title>SGConquers: Juego online</title>
<link rel="shortcut icon" href="../imagenes/favicon.ico">
<link rel="stylesheet" type="text/css" href="css/index.css">
<link rel="stylesheet" type="text/css" href="css/milkbox.css">
<link rel="stylesheet" href="css/formcheck.css" type="text/css" media="screen">
<link href="css/mobile.css" rel="stylesheet" type="text/css" media="handheld, only screen and (max-device-width: 480px)">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<body>
<div id="contenedor">
<div id="cabecera">
<div id="cabeceraCentro">
<div id="login">
<form action="index.php" method="post" id="frmLogin">
<div><input type="text" name="nick" id="usuario" title="Nombre de usuario" placeholder="Usuario"></div>
<div><input type="password" name="contraseña" id="pass" title="Contraseña" placeholder="Contraseña"></div>

<div><input type="submit" id="topsubmit" name="enviar" value="Enviar"></div>
<div id="olvidaste">¿Olvidaste tu contraseña?</div>
</form>
</div>
<div id="social">
<div>
<a href="#" target="_blank"><img src="imagenes/facebook.png" alt="Facebook" title="Facebook" border="0"></a>
</div>
<div>
<a href="#" target="_blank"><img src="imagenes/twitter.png" alt="Twitter" title="Twitter" border="0"></a>
</div>
<div>
<a href="#" target="_blank"><img src="imagenes/tuenti.png" alt="Tuenti" title="Tuenti" border="0"></a>
</div>
<div>
<a href="#" target="_blank"><img src="imagenes/googleplus.png" alt="Facebook" title="Google+" border="0"></a>
</div>
<div>

<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="PNBBMUX9P422N">
<!--<input type="image" src="https://www.paypal.com/es_ES/ES/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal. La forma rápida y segura de pagar en Internet.">-->
<input type="image" src="imagenes/donar.png" border="0" name="submit" alt="PayPal. La forma rápida y segura de pagar en Internet.">
<img alt="" border="0" src="imagenes/pixel.gif" width="1" height="1">
</form>
</div>
</div>
</div>
</div>
<div id="centro">
<div id="mensaje">Juego en fase Alpha pero desarrollandose a gran velocidad, en poco tiempo habrá beta abierta.</div>
<div id="menu">
<div id="loaderImagenes">
<img src="imagenes/boton1_over.jpg" alt="Boton1">
<img src="imagenes/boton2_over.jpg" alt="Boton2">
<img src="imagenes/boton3_over.jpg" alt="Boton3">
<img src="imagenes/boton4_over.jpg" alt="Boton4">
<img src="imagenes/boton5_over.jpg" alt="Boton5">
<img src="imagenes/boton6_over.jpg" alt="Boton6">
<img src="imagenes/boton7_over.jpg" alt="Boton7">
</div>
<div id="boton1" title="Formulario de registro">Registrarse</div>
<div id="boton2" title="Acerca del juego">Acerca de</div>
<div id="boton3" title="Foro del juego">Foro</div>
<div id="boton4" title="Ayuda del juego">Ayuda</div>
<div id="boton5" title="Imágenes del juego">Imágenes</div>
<div id="boton6" title="Blog de desarrollo">Blog</div>
<div id="boton7" title="Reporte de bugs">Bugs</div>
</div>
<div id="publicidad">

</div>
<div id="contenido" style="visibility: hidden; zoom: 1; opacity: 0;">
<div id="contenidoCabecera">
<div id="titulo">Titulo</div>
<img src="imagenes/close.png" alt="" id="botonCerrar">
</div>
<div id="contenidoContenedor">
<div id="preloader"></div>
<form action="#" id="frmRegistro" method="get">
<div id="interior"></div>
</form>
</div>
<div id="contenidoPie"></div>
</div>
</div>
<div id="pie">
<div id="pieMenu">
<div id="boton8" title="Términos &amp; Condiciones">T&amp;C</div>
<div id="boton9" title="Aviso legal">Aviso legal</div>
<div id="boton10" title="Reglas del juego">Reglas</div>
<div id="boton11" title="Créditos">Créditos</div>
</div>
<div id="copyright">

</div>
</div>
</div>

</body></html>


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