<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="formato.css" />
<title>SGConquers - Registro</title>
</head>

<body>

<div id="background">

<div id="fondo">


<div id="contenedor">

</div>

<div id="contenedor">

<div id="lateral">
<p>Enlaces:</p>
<p><a href="index.php">Inicio</a></p>
<p><a href="http://foro.sgconquers.hol.es">Foro</a></p>
</div>

<div id="principal">
<p>Puedes registrarte para participar en la primera alpha cerrada, además de efectuar el registro a través de este formulario, deberás registrarte en el foro y solicitar tu participación (si no lo has hecho ya), se te asignará un número y podrás jugar cuando te toque.
<?php
mysql_connect('LOCALHOST','USER','PASSWORD') or die ('Ha fallado la conexi&oacute;n:'.mysql_error());
mysql_select_db('DATABASE') or die ('Error al seleccionar la base de datos.'.mysql_error());


if (isset($_POST["nick"])) {
	$username = $_POST["nick"];
	$password = $_POST["contraseña"];
	$email = $_POST["email"];
	$raza = $_POST["raza"];
	
	$username = htmlentities($username);
	$password = htmlentities($password);
	$email = htmlentities($email);
	$raza = htmlentities($raza);
	$recursos = mysql_fetch_row(mysql_query("SELECT recurso1, recurso2 FROM raza WHERE nombre='$raza'"));
	
	if($username==NULL|$password==NULL|$email==NULL|$username>30|$password>30|$password2>30|$email>60) {
		echo "Algún campo del formulario est&aacute; vac&iacute;o o excede el tamaño permitido";
	}else{
			$checkuser = mysql_query("SELECT nick FROM jugadores WHERE nick='$username'");
			$username_exist = mysql_num_rows($checkuser);
			
			$checkemail = mysql_query("SELECT email FROM jugadores WHERE email ='$email'");
			$email_exist = mysql_num_rows($checkemail);
			
			if ($email_exist>0|$username_exist>0) {
				echo "El nombre de usuario o la cuenta de correo están ya en uso";
			}else{
				$query_planetas_libres = mysql_query('SELECT id FROM mapa WHERE dueño IS NULL') or die(mysql_error());
				$cantidad_planetas_libres = mysql_num_rows($query_planetas_libres);
				mt_srand(time());
				$random = mt_rand(0,$cantidad_planetas_libres - 1);
				$md5_password= md5($password);
				$query = 'INSERT INTO jugadores (nick, password, email, raza, recurso1, recurso2, planeta1) VALUES (\''.$username.'\',\''.$md5_password.'\',\''.$email.'\',\''.$raza.'\',\''.$recursos[0].'\',\''.$recursos[1].'\',\''.$random.'\')';
				$query_mapa = 'UPDATE mapa SET dueño=\''.$username.'\' WHERE id=\''.$random.'\'';
				
				mysql_query($query) or die(mysql_error());
				mysql_query($query_mapa) or die(mysql_error());
				echo '<script type="text/javascript">alert("El jugador '.$username.' ha sido registrado de manera satisfactoria. Podrá entrar en cuanto su cuenta haya sido activada");</script>';
				
				echo '<SCRIPT LANGUAJE="javascript">';
				echo 'location.href = "index.php";';
				echo '</SCRIPT>';
		}
	}
}
	?>
<form method="post" action="registro.php">
<p>Usuario:<input type="text" name="nick" /></p>
<p>E-mail:<input type="email" name="email" /></p>
<p>Contraseña:<input type="password" name="contraseña" /></p>
<p>Raza:<select name="raza">
	<option value="tauri">Tauri</option>
    <option value="wraith">Wraith</option>
    <option value="asgard">Asgard</option>
    <option value="goauld">Goauld</option>
    <option value="atlante">Atlante</option>
    <option value="jaffa">Jaffa</option>
    </select></p>
<p><input type="submit" value="Enviar" /></p>
</form>
</p>
</div>

<div id="pie">
SGconquers es un proyecto opensource y sin &aacute;nimo de lucro, cualquiera que desee ayudar es bienvenid@.
</div>


</div>


</div>
</div>
</body>
</html>
