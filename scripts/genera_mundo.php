<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body>
<form method="post" action="genera_mundo.php">
<p>Galaxia:<input type="text" name="galaxia" /></p>
<p>numero sectores:<input type="text" name="sectores" /></p>
<p>numero cuadrantes:<input type="text" name="cuadrantes" /></p>
<p>numero planetas:<input type="text" name="planetas" /></p>
<input type="submit" value="Enviar" /></p>
</form>
<?php
if (isset($_POST["galaxia"])) {
mysql_connect('localhost','USER','PASSWORD') or die ('Ha fallado la conexi&oacute;n:'.mysql_error());
mysql_select_db('DATABASE') or die ('Error al seleccionar la base de datos.'.mysql_error());
$galaxia = $_POST["galaxia"];
$sectores = $_POST["sectores"];
$cuadrantes = $_POST["cuadrantes"];
$planetas = $_POST["planetas"];
	for ($i = 1; $i <= $sectores; $i++) {
		for ($j = 1; $j <= $cuadrantes; $j++) {
			for ($k = 1; $k <= $planetas; $k++) {
				$random = rand(1,10);
				echo "<p>".$random."</p>";
				$porcentaje = ($random * 10);
				echo "<p>".$porcentaje."</p>";
				$nombre = $i.'-'.$j.'-'.$k;
				mysql_query('INSERT INTO mapa (galaxia, sector, cuadrante, posicion, porcentaje, nombre) VALUES (\''.$galaxia.'\',\''.$i.'\',\''.$j.'\',\''.$k.'\',\''.$porcentaje.'\',\''.$nombre.'\')') or die(mysql_error());
			}
		}
	}
}

?>
</body>
</html>