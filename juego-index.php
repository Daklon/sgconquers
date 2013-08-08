<?php session_start(); 
mysql_connect('LOCALHOST','USER','PASSWORD') or die ('Ha fallado la conexi&oacute;n:'.mysql_error());
mysql_select_db('DATABASE') or die ('Error al seleccionar la base de datos.'.mysql_error());

//almacena datos importantes sobre el usuario
$query_jugador_dat = mysql_query('SELECT recurso1, recurso2, inv1, inv2, inv3, inv4, inv5, inv6, inv7, inv8, inv9, inv10, inv11, inv12, inv13, nick, codigoseguridad, planeta1, planeta2, planeta3, planeta4, planeta5 FROM jugadores WHERE nick =\''.$_SESSION['nick'].'\' AND codigoseguridad =\''.$_SESSION["codigoseguridad"].'\'') or die (mysql_error());
$jugador_dat = mysql_fetch_array($query_jugador_dat);
$query_energia_inicial = mysql_query('SELECT energia FROM raza WHERE nombre =\''.$_SESSION['raza'].'\'') or die (mysql_error());
$energia_inicial = mysql_fetch_row($query_energia_inicial);
$query_inves_energ = mysql_query('SELECT numero, efecenergia FROM investigaciones WHERE raza =\''.$_SESSION["raza"].'\' AND efecenergia IS NOT NULL') or die (mysql_error());
$inves_energ = mysql_fetch_row($query_inves_energ);

//calcula la cantidad de energía total
$energia_total = $energia_inicial[0];
for ($i = 0; $i < $jugador_dat[$inves_energ[0] + 1];$i++){
	$energia_total = $energia_total + $energia_total * $inves_energ[1] / 100;
}
//función que calcula el tiempo de las investigaciones a medida que aumenta el nivel
function tiempoinves($tiempo_inicial, $nivel){
	
	for($i = 0; $i < $nivel; $i++){
		$tiempo_inicial = $tiempo_inicial * (1.84 * pow($nivel, -0.1201));
	}
	$tiempo_inicial = round($tiempo_inicial, 0, PHP_ROUND_HALF_UP);
	return $tiempo_inicial;
}

function convierteseg($tiempo_total){
if ($tiempo_total >= 86400){
		$dias = floor($tiempo_total/ 86400);
		$horas = floor($tiempo_total / 3600 - ($dias *24));
		$minutos = floor($tiempo_total/60 - ($dias * 24 * 60));
		$segundos = $tiempo_total % 60;
		if($dias > 1){
			echo $dias.' dias'.$horas.':'.$minutos.':'.$segundos;
		}else{
			echo $dias.' dia'.$horas.':'.$minutos.':'.$segundos;
		}
	}else if($tiempo_total >= 3600){
		$horas = floor($tiempo_total / 3600 );
		$minutos = floor($tiempo_total/60 - ($horas * 60));
		$segundos = $tiempo_total % 60;
		echo $horas.':'.$minutos.':'.$segundos;
	}else if($tiempo_total >= 60){
		$minutos = floor($tiempo_total/60);
		$segundos = $tiempo_total % 60;
		echo $minutos.':'.$segundos;
	}else if($tiempo_total < 60){
		$segundos = $tiempo_total;
		echo $segundos.' Segundos';
	}
}
//comprueba que el usuario está logueado y es correcto
if($_SESSION["nick"] == $jugador_dat["nick"] && $_SESSION["codigoseguridad"] == $jugador_dat["codigoseguridad"] && isset($_SESSION["nick"]) && isset($_SESSION["codigoseguridad"])){
	//comprueba si se ha cambiado de planeta
	if(isset($_POST["planeta"])){
		$_SESSION["planeta"] = $_POST["planeta"];
	}else{
		$_SESSION["planeta"] = $jugador_dat["planeta1"];
	}
	//comprueba si se ha solicitado subir de nivel una investigación, en caso de que así sea ejecuta las consultas a la bd pertinentes
	if(isset($_POST["subirnivel"])){
		$query_inves_pend = mysql_query('SELECT id FROM inves_pend WHERE jugador=\''.$_SESSION['nick'].'\' AND cancelado=\'0\'')or die(mysql_error());
		$inves_pend = mysql_fetch_row($query_inves_pend);
		if (empty($inves_pend[0])){
		$cancelado = 0;
		$hora_fin_inves = time() + $_POST["tiempo"];
		mysql_query('INSERT INTO inves_pend (cancelado, jugador, investigacion, nivelnuevo, horafinalizar) VALUES (\''.$cancelado.'\', \''.$_SESSION["nick"].'\', \''.$_POST["numero_inves"].'\', \''.$_POST["nuevo_nivel"].'\', \''.$hora_fin_inves.'\')') or die(mysql_error());
		//consulta recursos con una query y posteriormente restar la cantidad necesaria a esos recursos y añadirlos a la bd
		$query_recursos_actualizados = mysql_query('SELECT recurso1, recurso2 FROM jugadores WHERE nick = \''.$_SESSION["nick"].'\'') or die(mysql_error());
		$recursos_actualizados = mysql_fetch_row($query_recursos_actualizados);
		$jugador_dat[0] = $recursos_actualizados[0] - $_POST["recurso1"];
		$jugador_dat[1] = $recursos_actualizados[1] - $_POST["recurso2"];
		mysql_query('UPDATE jugadores SET recurso1=\''.$jugador_dat[0].'\', recurso2=\''.$jugador_dat[1].'\' WHERE nick = \''.$_SESSION["nick"].'\'') or die(mysql_error());
		}
	}
	//comprueba si se ha enviado la orden de cancelar, en cuyo caso cancela
	if (isset($_POST["cancelar_inves"])){
		mysql_query('UPDATE inves_pend SET cancelado =\'1\' WHERE jugador=\''.$_SESSION["nick"].'\' AND cancelado=\'0\'') or die(mysql_error());
	}
	
	//añade la tarea de crear tropas
	if (isset($_POST["crear_tropa"])){
		$query_cons_pend = mysql_query('SELECT id FROM cons_pend WHERE jugador=\''.$_SESSION['nick'].'\' AND cancelado=\'0\' AND planeta=\''.$_SESSION['planeta'].'\' AND tipo=\'tropas\'')or die(mysql_error());
		$cons_pend = mysql_fetch_row($query_cons_pend);
		if (empty($cons_pend[0])){
		$cancelado = 0;
		$hora_fin_cons = time() + ($_POST["tiempo"]* $_POST["cantidad"]);
		mysql_query('INSERT INTO cons_pend (cancelado, planeta, jugador, horafinalizar, cantidad, unidad, tipo) VALUES (\''.$cancelado.'\', \''.$_SESSION["planeta"].'\', \''.$_SESSION["nick"].'\', \''.$hora_fin_cons.'\', \''.$_POST["cantidad"].'\', \''.$_POST["nombre"].'\', \'tropas\')') or die(mysql_error());
		//consulta recursos con una query y posteriormente restar la cantidad necesaria a esos recursos y añadirlos a la bd
		$query_recursos_actualizados = mysql_query('SELECT recurso1, recurso2 FROM jugadores WHERE nick = \''.$_SESSION["nick"].'\'') or die(mysql_error());
		$recursos_actualizados = mysql_fetch_row($query_recursos_actualizados);
		$jugador_dat[0] = $recursos_actualizados[0] - ($_POST["recurso1"] * $_POST["cantidad"]);
		$jugador_dat[1] = $recursos_actualizados[1] - ($_POST["recurso2"] * $_POST["cantidad"]);
		mysql_query('UPDATE jugadores SET recurso1=\''.$jugador_dat[0].'\', recurso2=\''.$jugador_dat[1].'\' WHERE nick = \''.$_SESSION["nick"].'\'') or die(mysql_error());
		}
	}
	
	//añade la tarea de crear naves
	if (isset($_POST["crear_nave"])){
		$query_cons_pend = mysql_query('SELECT id FROM cons_pend WHERE jugador=\''.$_SESSION['nick'].'\' AND cancelado=\'0\' AND planeta=\''.$_SESSION['planeta'].'\' AND tipo=\'naves\'')or die(mysql_error());
		$cons_pend = mysql_fetch_row($query_cons_pend);
		if (empty($cons_pend[0])){
		$cancelado = 0;
		$hora_fin_cons = time() + ($_POST["tiempo"]* $_POST["cantidad"]);
		mysql_query('INSERT INTO cons_pend (cancelado, planeta, jugador, horafinalizar, cantidad, unidad, tipo) VALUES (\''.$cancelado.'\', \''.$_SESSION["planeta"].'\', \''.$_SESSION["nick"].'\', \''.$hora_fin_cons.'\', \''.$_POST["cantidad"].'\', \''.$_POST["nombre"].'\', \'naves\')') or die(mysql_error());
		//consulta recursos con una query y posteriormente restar la cantidad necesaria a esos recursos y añadirlos a la bd
		$query_recursos_actualizados = mysql_query('SELECT recurso1, recurso2 FROM jugadores WHERE nick = \''.$_SESSION["nick"].'\'') or die(mysql_error());
		$recursos_actualizados = mysql_fetch_row($query_recursos_actualizados);
		$jugador_dat[0] = $recursos_actualizados[0] - ($_POST["recurso1"] * $_POST["cantidad"]);
		$jugador_dat[1] = $recursos_actualizados[1] - ($_POST["recurso2"] * $_POST["cantidad"]);
		mysql_query('UPDATE jugadores SET recurso1=\''.$jugador_dat[0].'\', recurso2=\''.$jugador_dat[1].'\' WHERE nick = \''.$_SESSION["nick"].'\'') or die(mysql_error());
		}
	}
	
	//añade la tarea de crear defensas
	if (isset($_POST["crear_defensa"])){
		$query_cons_pend = mysql_query('SELECT id FROM cons_pend WHERE jugador=\''.$_SESSION['nick'].'\' AND cancelado=\'0\' AND planeta=\''.$_SESSION['planeta'].'\' AND tipo=\'defensas\'')or die(mysql_error());
		$cons_pend = mysql_fetch_row($query_cons_pend);
		if (empty($cons_pend[0])){
		$cancelado = 0;
		$hora_fin_cons = time() + ($_POST["tiempo"]* $_POST["cantidad"]);
		mysql_query('INSERT INTO cons_pend (cancelado, planeta, jugador, horafinalizar, cantidad, unidad, tipo) VALUES (\''.$cancelado.'\', \''.$_SESSION["planeta"].'\', \''.$_SESSION["nick"].'\', \''.$hora_fin_cons.'\', \''.$_POST["cantidad"].'\', \''.$_POST["nombre"].'\', \'defensas\')') or die(mysql_error());
		//consulta recursos con una query y posteriormente restar la cantidad necesaria a esos recursos y añadirlos a la bd
		$query_recursos_actualizados = mysql_query('SELECT recurso1, recurso2 FROM jugadores WHERE nick = \''.$_SESSION["nick"].'\'') or die(mysql_error());
		$recursos_actualizados = mysql_fetch_row($query_recursos_actualizados);
		$jugador_dat[0] = $recursos_actualizados[0] - ($_POST["recurso1"] * $_POST["cantidad"]);
		$jugador_dat[1] = $recursos_actualizados[1] - ($_POST["recurso2"] * $_POST["cantidad"]);
		mysql_query('UPDATE jugadores SET recurso1=\''.$jugador_dat[0].'\', recurso2=\''.$jugador_dat[1].'\' WHERE nick = \''.$_SESSION["nick"].'\'') or die(mysql_error());
		}
	}
	
	//comprueba si se ha cancelado alguna construcción de tropas
	if (isset($_POST["cancelar_tropas"])){
		mysql_query('UPDATE cons_pend SET cancelado =\'1\' WHERE jugador=\''.$_SESSION["nick"].'\' AND cancelado=\'0\' AND planeta =\''.$_SESSION["planeta"].'\' AND tipo=\'tropas\'') or die(mysql_error());
	}
	//comprueba si se ha cancelado alguna construcción de naves
	if (isset($_POST["cancelar_naves"])){
		mysql_query('UPDATE cons_pend SET cancelado =\'1\' WHERE jugador=\''.$_SESSION["nick"].'\' AND cancelado=\'0\' AND planeta =\''.$_SESSION["planeta"].'\' AND tipo=\'naves\'') or die(mysql_error());
	}
	//comprueba si se ha cancelado alguna construcción de defensas
	if (isset($_POST["cancelar_defensas"])){
		mysql_query('UPDATE cons_pend SET cancelado =\'1\' WHERE jugador=\''.$_SESSION["nick"].'\' AND cancelado=\'0\' AND planeta =\''.$_SESSION["planeta"].'\' AND tipo=\'defensas\'') or die(mysql_error());
	}
	
	//comprueba si se han enviado mensajes
	if (isset($_POST["enviar_mensaje"])){
		$remitente = htmlentities($_POST["remitente"]);
		$destinatario = htmlentities($_POST["destinatario"]);
		$hora = htmlentities($_POST["hora"]);
		$asunto = htmlentities($_POST["asunto"]);
		$cuerpo = htmlentities($_POST["cuerpo"]);
		mysql_query('INSERT INTO mensajes (de, para, hora, asunto, mensaje) VALUES (\''.$remitente.'\', \''.$destinatario.'\',\''.$hora.'\',\''.$asunto.'\',\''.$cuerpo.'\')') or die(mysql_error());
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="formato.css" />
<title>SGConquers - <?php echo $_SESSION["raza"]; ?></title>
</head>

<body>
<div id="background">

<div id="fondo">


<div id="contenedor">
<div id="recursos">
<!--imprime los recursos y la energía-->
Recurso1:<?php  printf("%s",$jugador_dat[0]);?> Recurso2:<?php printf("%s",$jugador_dat[1]); ?> Energía:<?php  printf("%s", round($energia_total, 0, PHP_ROUND_HALF_UP)); ?>
</div>
<div id="planeta">
<!-- lista de selección de planeta(falta la parte de mostrar el nombre en lugar del id) -->
<form method="post" action="juego-index.php" name="selec_planeta">
<select name="planeta" onchange="document.selec_planeta.submit();">
<?php
for($i=1;$i <= 5;$i++){
	if ($jugador_dat[$i + 16] != NULL){
		echo '<option value="'.$jugador_dat[$i + 16].'"';
		if($_SESSION["planeta"] == $jugador_dat[$i + 16]){
			echo ' selected>';
		}else{
			echo '>';
		}
		printf("%s",$jugador_dat[$i + 16]);
		echo '</option>';
	}
}
?>
</select>
</form>
</div>

</div>

<div id="contenedor">
<?php
//todos estos if comprueban si se ha pulsado cada uno de los enlaces de los apartados
if($_GET["control"] == "salir"){
	$codigodefecto = "codigoseguridad32digitosusuarioh";
	mysql_query('UPDATE jugadores SET codigoseguridad=\''.$codigodefecto.'\' WHERE nick =\''.$_SESSION['nick'].'\' ')or die (mysql_error());
	session_destroy();
	echo '<SCRIPT LANGUAJE="javascript">';
	echo 'location.href = "index.php";';
	echo '</SCRIPT>';

}
?>
<div id="lateral">
<p>Enlaces:</p>
<p><a href="juego-index.php?control=inicio">Inicio</a></p>
<p><a href="juego-index.php?control=investiga">Investigaciones</a></p>
<p><a href="juego-index.php?control=recursos">Recursos</a></p>
<p><a href="juego-index.php?control=tropas">Tropas</a></p>
<p><a href="juego-index.php?control=naves">Naves</a></p>
<p><a href="juego-index.php?control=defensas">Defensas</a></p>
<p><a href="juego-index.php?control=galaxia">Galaxia</a></p>
<p><a href="juego-index.php?control=alianza">Alianza</a></p>
<p><a href="juego-index.php?control=mensajes">Mensajes</a></p>
<p><a href="juego-index.php?control=ranking">Ranking</a></p>
<p><a href="juego-index.php?control=opciones">Opciones</a></p>
<p><a href="http://foro.sgconquers.hol.es">Foro</a></p>
<p><a href="juego-index.php?control=salir">Salir</a></p>

</div>
<?php 
//aquí falta mucho por añadir
if($_GET["control"] == "inicio" || $_GET["control"] == ""){
	?>
	<div id="principal">
<p>Juego en fase pre-Alpha, aún no está ni de lejos listo para ser jugado, pero quiero ir haciendo pruebas para probar lo que tengo hecho, si todo va bien no debería haber ningún error.</p>
</div>
<?php
//genera caja investigación si es necesario
$query_inves_pend = mysql_query('SELECT investigacion, nivelnuevo, horafinalizar FROM inves_pend WHERE jugador=\''.$_SESSION['nick'].'\' AND cancelado=\'0\'')or die(mysql_error());
$inves_pend = mysql_fetch_array($query_inves_pend);
	$query_nombre_inves = mysql_query('SELECT nombre FROM investigaciones WHERE raza=\''.$_SESSION['raza'].'\' AND numero=\''.$inves_pend["investigacion"].'\'')or die(mysql_error());
	$nombre_inves = mysql_fetch_array($query_nombre_inves);
	
	
	if(!empty($inves_pend)){
		$tiempo_restante = $inves_pend["horafinalizar"] - time();
		?>
    <script type="text/javascript" language="JavaScript"> 
var tiempo = <?php echo $tiempo_restante; ?>;
function contador(){
	if(tiempo >= 86400){
		var dias =  Math.floor(tiempo / 86400);
		var horas = Math.floor(tiempo / 3600- (dias *24));
		var minutos = tiempo/60 - (dias * 24 * 60);
		var minutos = Math.floor(minutos);
		var segundos = tiempo % 60;
		if(dias > 1){
			document.formulario.reloj.value=dias + " dias "+ horas + ":" +minutos +":"+ segundos;
		}else{
			document.formulario.reloj.value=dias + " dia "+ horas + ":" +minutos +":"+ segundos;
		}
		
	}else if (tiempo >= 3600){
		var horas = Math.floor(tiempo / 3600);
		var minutos = tiempo/60 - (horas * 60);
		var minutos = Math.floor(minutos);
		var segundos = tiempo % 60;
		document.formulario.reloj.value= horas + ":" +minutos +":"+ segundos;
	}else if (tiempo >= 60){
		var minutos = tiempo/60;
		var minutos = Math.floor(minutos);
		var segundos = tiempo % 60;
		document.formulario.reloj.value= minutos +":"+ segundos;
	}else if(tiempo < 60){
		var segundos = tiempo;
		document.formulario.reloj.value= segundos;
	}
	tiempo--;
	if (tiempo < 0){
		location.reload();
	}
}
window.onload = contador;

setInterval("contador()",1000);  
</script> 
    <?php
		echo '<div id="principal">';
		echo '<table><tr>';
		echo '<td>Investigación: '.$nombre_inves["nombre"].'('.$inves_pend["nivelnuevo"].')</td>';
		echo '<td><form name="formulario"><input type="text" name="reloj" value="" size="55" style="background-color:#006; color:#FFF; border : 0px ; text-align : center"></form> </td>';
		echo '</tr></table></div>';
	}
	
	//genera caja para construcción tropas si es necesario
	$query_cons_pend = mysql_query('SELECT unidad, cantidad, horafinalizar FROM cons_pend WHERE jugador=\''.$_SESSION['nick'].'\' AND cancelado=\'0\' AND planeta=\''.$_SESSION["planeta"].'\' AND tipo=\'tropas\'')or die(mysql_error());
$cons_pend = mysql_fetch_array($query_cons_pend);
	
	if(!empty($cons_pend)){
		$tiempo_restante = $cons_pend["horafinalizar"] - time();
		?>
    <script type="text/javascript" language="JavaScript"> 
var tiempo = <?php echo $tiempo_restante; ?>;
function contador(){
	if(tiempo >= 86400){
		var dias =  Math.floor(tiempo / 86400);
		var horas = Math.floor(tiempo / 3600- (dias *24));
		var minutos = tiempo/60 - (dias * 24 * 60);
		var minutos = Math.floor(minutos);
		var segundos = tiempo % 60;
		if(dias > 1){
			document.formulario.reloj.value=dias + " dias "+ horas + ":" +minutos +":"+ segundos;
		}else{
			document.formulario.reloj.value=dias + " dia "+ horas + ":" +minutos +":"+ segundos;
		}
		
	}else if (tiempo >= 3600){
		var horas = Math.floor(tiempo / 3600);
		var minutos = tiempo/60 - (horas * 60);
		var minutos = Math.floor(minutos);
		var segundos = tiempo % 60;
		document.formulario.reloj.value= horas + ":" +minutos +":"+ segundos;
	}else if (tiempo >= 60){
		var minutos = tiempo/60;
		var minutos = Math.floor(minutos);
		var segundos = tiempo % 60;
		document.formulario.reloj.value= minutos +":"+ segundos;
	}else if(tiempo < 60){
		var segundos = tiempo;
		document.formulario.reloj.value= segundos;
	}
	tiempo--;
	if (tiempo < 0){
		location.reload();
	}
}
window.onload = contador;

setInterval("contador()",1000);  
</script> 
    <?php
		echo '<div id="principal">';
		echo '<table><tr>';
		echo '<td> '.$cons_pend["unidad"].'('.$cons_pend["cantidad"].')</td>';
		echo '<td><form name="formulario"><input type="text" name="reloj" value="" size="55" style="background-color:#006; color:#FFF; border : 0px ; text-align : center"></form> </td>';
		echo '</tr></table></div>';
	}
	
	//genera caja para construcción navess si es necesario
	$query_cons_pend = mysql_query('SELECT unidad, cantidad, horafinalizar FROM cons_pend WHERE jugador=\''.$_SESSION['nick'].'\' AND cancelado=\'0\' AND planeta=\''.$_SESSION["planeta"].'\' AND tipo=\'naves\'')or die(mysql_error());
$cons_pend = mysql_fetch_array($query_cons_pend);
	
	if(!empty($cons_pend)){
		$tiempo_restante = $cons_pend["horafinalizar"] - time();
		?>
    <script type="text/javascript" language="JavaScript"> 
var tiempo = <?php echo $tiempo_restante; ?>;
function contador(){
	if(tiempo >= 86400){
		var dias =  Math.floor(tiempo / 86400);
		var horas = Math.floor(tiempo / 3600- (dias *24));
		var minutos = tiempo/60 - (dias * 24 * 60);
		var minutos = Math.floor(minutos);
		var segundos = tiempo % 60;
		if(dias > 1){
			document.formulario.reloj.value=dias + " dias "+ horas + ":" +minutos +":"+ segundos;
		}else{
			document.formulario.reloj.value=dias + " dia "+ horas + ":" +minutos +":"+ segundos;
		}
		
	}else if (tiempo >= 3600){
		var horas = Math.floor(tiempo / 3600);
		var minutos = tiempo/60 - (horas * 60);
		var minutos = Math.floor(minutos);
		var segundos = tiempo % 60;
		document.formulario.reloj.value= horas + ":" +minutos +":"+ segundos;
	}else if (tiempo >= 60){
		var minutos = tiempo/60;
		var minutos = Math.floor(minutos);
		var segundos = tiempo % 60;
		document.formulario.reloj.value= minutos +":"+ segundos;
	}else if(tiempo < 60){
		var segundos = tiempo;
		document.formulario.reloj.value= segundos;
	}
	tiempo--;
	if (tiempo < 0){
		location.reload();
	}
}
window.onload = contador;

setInterval("contador()",1000);  
</script> 
    <?php
		echo '<div id="principal">';
		echo '<table><tr>';
		echo '<td> '.$cons_pend["unidad"].'('.$cons_pend["cantidad"].')</td>';
		echo '<td><form name="formulario"><input type="text" name="reloj" value="" size="55" style="background-color:#006; color:#FFF; border : 0px ; text-align : center"></form> </td>';
		echo '</tr></table></div>';
	}
	
	//genera caja para construcción defensas si es necesario
	$query_cons_pend = mysql_query('SELECT unidad, cantidad, horafinalizar FROM cons_pend WHERE jugador=\''.$_SESSION['nick'].'\' AND cancelado=\'0\' AND planeta=\''.$_SESSION["planeta"].'\' AND tipo=\'defensas\'')or die(mysql_error());
$cons_pend = mysql_fetch_array($query_cons_pend);
	
	if(!empty($cons_pend)){
		$tiempo_restante = $cons_pend["horafinalizar"] - time();
		?>
    <script type="text/javascript" language="JavaScript"> 
var tiempo = <?php echo $tiempo_restante; ?>;
function contador(){
	if(tiempo >= 86400){
		var dias =  Math.floor(tiempo / 86400);
		var horas = Math.floor(tiempo / 3600- (dias *24));
		var minutos = tiempo/60 - (dias * 24 * 60);
		var minutos = Math.floor(minutos);
		var segundos = tiempo % 60;
		if(dias > 1){
			document.formulario.reloj.value=dias + " dias "+ horas + ":" +minutos +":"+ segundos;
		}else{
			document.formulario.reloj.value=dias + " dia "+ horas + ":" +minutos +":"+ segundos;
		}
		
	}else if (tiempo >= 3600){
		var horas = Math.floor(tiempo / 3600);
		var minutos = tiempo/60 - (horas * 60);
		var minutos = Math.floor(minutos);
		var segundos = tiempo % 60;
		document.formulario.reloj.value= horas + ":" +minutos +":"+ segundos;
	}else if (tiempo >= 60){
		var minutos = tiempo/60;
		var minutos = Math.floor(minutos);
		var segundos = tiempo % 60;
		document.formulario.reloj.value= minutos +":"+ segundos;
	}else if(tiempo < 60){
		var segundos = tiempo;
		document.formulario.reloj.value= segundos;
	}
	tiempo--;
	if (tiempo < 0){
		location.reload();
	}
}
window.onload = contador;

setInterval("contador()",1000);  
</script> 
    <?php
		echo '<div id="principal">';
		echo '<table><tr>';
		echo '<td> '.$cons_pend["unidad"].'('.$cons_pend["cantidad"].')</td>';
		echo '<td><form name="formulario"><input type="text" name="reloj" value="" size="55" style="background-color:#006; color:#FFF; border : 0px ; text-align : center"></form> </td>';
		echo '</tr></table></div>';
	} 
?>

<?php
}else if($_GET["control"] == "investiga"){
	$inves_query = mysql_query('SELECT nombre, descripcion, recurso1, recurso2, tiempo, numero FROM investigaciones WHERE raza =\''.$_SESSION["raza"].'\'') or die (mysql_error());
	$query_inves_pend = mysql_query('SELECT id, investigacion, horafinalizar FROM inves_pend WHERE jugador=\''.$_SESSION['nick'].'\' AND cancelado=\'0\'')or die(mysql_error());
	$inves_pend = mysql_fetch_row($query_inves_pend);

?>
<div id="principal">
<!-- Script encargado de separar las investigaciones por apartados-->
<script>
function cambio(numero){
	switch (numero){
		case 1:
			document.getElementById("generales").style.display = 'block';
			document.getElementById("tropas").style.display = 'none';
			document.getElementById("naves").style.display = 'none';
			document.getElementById("defensas").style.display = 'none';
		break;	
		case 2:
			document.getElementById("generales").style.display = 'none';
			document.getElementById("tropas").style.display = 'block';
			document.getElementById("naves").style.display = 'none';
			document.getElementById("defensas").style.display = 'none';
		break;
		case 3:
			document.getElementById("generales").style.display = 'none';
			document.getElementById("tropas").style.display = 'none';
			document.getElementById("naves").style.display = 'block';
			document.getElementById("defensas").style.display = 'none';
		break;
		case 4:
			document.getElementById("generales").style.display = 'none';
			document.getElementById("tropas").style.display = 'none';
			document.getElementById("naves").style.display = 'none';
			document.getElementById("defensas").style.display = 'block';	
	}
}
</script>
	<?php
	//bucle que muestra todas las investigaciones accesibles(falta dividirlos por apartados)
	$indice = 2;
	while($inves_dat = mysql_fetch_assoc($inves_query)){
	?>
	<div id="investigacion">
   	<?php
	//calcula el tiempo de la investigación en cuestión
	$tiempo_total = tiempoinves($inves_dat["tiempo"], $jugador_dat[$indice]);
	echo '<p>';
	//imprime el nombre junto al nivel
		printf("%s(%s)",$inves_dat["nombre"],$jugador_dat[$indice]);
	echo '<div id="botonmejora">';
	//si se tienen los recursos y no se ha iniciado otra investigación, imprime el form para subir nivel, si no muestra el mensaje de notienes recursos
	if ($jugador_dat["recurso1"] >= ($inves_dat["recurso1"] * pow(2, $jugador_dat[$indice])) && $jugador_dat["recurso2"] >= ($inves_dat["recurso2"] * pow(2, $jugador_dat[$indice])) && empty($inves_pend[0])){
    	echo '<form action="juego-index.php?control=investiga" method="post">';
		echo '<input type="submit" value="Subir Nivel" name="subirnivel">';
		echo '<input type="hidden" value="'.$inves_dat["numero"].'" name="numero_inves">';
		echo '<input type="hidden" value="'.($jugador_dat["inv".$inves_dat["numero"]] + 1) .'" name="nuevo_nivel">';
		echo '<input type="hidden" value="'.$tiempo_total.'" name="tiempo">';
		echo '<input type="hidden" value="'.($inves_dat["recurso1"] * pow(2, $jugador_dat[$indice])).'" name="recurso1">';
		echo '<input type="hidden" value="'.($inves_dat["recurso2"] * pow(2, $jugador_dat[$indice])).'" name="recurso2">';
		echo '</form>';
	}else if(!empty($inves_pend[0]) && ($indice - 1 != $inves_pend[1])){
		echo '<p>Ya tienes una investigación en curso</p>';
	}else if(($indice - 1 == $inves_pend[1])){
		$tiempo_restante = $inves_pend[2] - time();
		?>
    <script type="text/javascript" language="JavaScript"> 
var tiempo = <?php echo $tiempo_restante; ?>;
function contador(){
	if(tiempo >= 86400){
		var dias =  Math.floor(tiempo / 86400);
		var horas = Math.floor(tiempo / 3600- (dias *24));
		var minutos = tiempo/60 - (dias * 24 * 60);
		var minutos = Math.floor(minutos);
		var segundos = tiempo % 60;
		if(dias > 1){
			document.formulario.reloj.value=dias + " dias "+ horas + ":" +minutos +":"+ segundos;
		}else{
			document.formulario.reloj.value=dias + " dia "+ horas + ":" +minutos +":"+ segundos;
		}
		
	}else if (tiempo >= 3600){
		var horas = Math.floor(tiempo / 3600);
		var minutos = tiempo/60 - (horas * 60);
		var minutos = Math.floor(minutos);
		var segundos = tiempo % 60;
		document.formulario.reloj.value= horas + ":" +minutos +":"+ segundos;
	}else if (tiempo >= 60){
		var minutos = tiempo/60;
		var minutos = Math.floor(minutos);
		var segundos = tiempo % 60;
		document.formulario.reloj.value= minutos +":"+ segundos;
	}else if(tiempo < 60){
		var segundos = tiempo;
		document.formulario.reloj.value= segundos;
	}
	tiempo--;
	if (tiempo < 0){
		location.reload();
	}
}
window.onload = contador;

setInterval("contador()",1000);  
</script> 
    <?php
		echo '<form name="formulario"><input type="text" name="reloj" value="" size="25" style="background-color:#006; color:#FFF; border : 0px ; text-align : center"></form>';
		echo '<form action="juego-index.php?control=investiga" method="post">';
		echo '<input type="submit" value="Cancelar" name="cancelar_inves">';
		echo '</form>';		
	}else{
		echo '<p>No tienes suficientes recursos</p>';	
	}
    echo '</div>';
	echo '</p>';
	echo '<p>';
	//imprime la descripción de la investigación
		printf("%s",$inves_dat["descripcion"]);
	echo '</p>';
	echo '<p>';
	//imprime cuanto cuesa en el recurso1
		printf("recurso1: %s",($inves_dat["recurso1"] * pow(2, $jugador_dat[$indice])));
	echo '</p>';
	echo '<p>';
	//imprime cuanto cuesta en el recurso 2
		printf("recurso2: %s",($inves_dat["recurso2"] * pow(2, $jugador_dat[$indice])));
	echo '</p>';
	echo '<p>';
	//imprime cuanto tarda(falta añadir función que devuelva el tiempo en horas, minutos y segundos
	echo 'Tiempo: ';
	convierteseg($tiempo_total);
	echo '</p>';
	?>
    
    </div>
    <?php
	//esto es para que se salte los dos primeros campos que no son investigaciones)
	$indice++;
	}
	?>
</div>
<?php
}else if($_GET["control"] == "recursos"){
?>
	<div id="principal">
<p>recursos.</p>
</div>
<?php
}else if($_GET["control"] == "tropas"){
?>
<div id="principal">
<!-- Script encargado de separar las tropas existentes de las nuevas -->
<script>
function cambio(numero){
	switch (numero){
		case 1:
			document.getElementById("actuales").style.display = 'block';
			document.getElementById("nuevas").style.display = 'none';
			document.getElementById("requisitos").style.display = 'none';
		break;	
		case 2:
			document.getElementById("actuales").style.display = 'none';
			document.getElementById("nuevas").style.display = 'block';
			document.getElementById("requisitos").style.display = 'none';
		break;
		case 3:
			document.getElementById("actuales").style.display = 'none';
			document.getElementById("nuevas").style.display = 'none';
			document.getElementById("requisitos").style.display = 'block';
		break;
	}
}
</script>
<div id="cambio">
<table>
<tr>
<td><a onclick="cambio(1)" href="#">Tropas en el Planeta</a></td>
<td><a onclick="cambio(2)" href="#">Construir Tropas</a></td>
<td><a onclick="cambio(3)" href="#">Requisitos</a></td>
</tr>
</table>
</div>
<div id="actuales" >
<?php
//selecciona todas las tropas en el planeta y las muestra(falta filtrar solo las del dueño del planeta)
$query_tropas = mysql_query('SELECT * FROM existencias_tropas WHERE planetaactual=\''.$_SESSION["planeta"].'\' AND dueño=\''.$_SESSION["nick"].'\'') or die (mysql_error());
$datos_tropas = mysql_fetch_array($query_tropas);
//si no hay tropas muestra un mensaje, si las hay las muestra
if(empty($datos_tropas)){
	echo "<p>No tienes tropas en este planeta</p>";
}else{
$columnas = array_keys($datos_tropas);
$query_tropas_actuales = mysql_query('SELECT ataque, defensa, escudo, carga, autodestruccion, atraviesa_escudos, camuflaje, atraviesa_stargate FROM unidades WHERE tipo=\'tierra\'');
$tropas_actuales = mysql_fetch_array($query_tropas_actuales);
	for($i = 0;$i < mysql_num_fields($query_tropas);$i++){
		if($i >= 3){
			if ($datos_tropas[$i] != NULL){
				echo '<div id="trop">';
				echo " ".$columnas[($i*2)+1].":";
				echo " ".$datos_tropas[$i];
				echo '</div>';
			}
		}
	}
}
?>
</div>
<div id="nuevas">
<script>
function justNumbers(e) {
var keynum = window.event ? window.event.keyCode : e.which;
if ( keynum == 8 ) return true;
return /\d/.test(String.fromCharCode(keynum));
}
</script>
<?php
$query_nuevas_tropas = mysql_query('SELECT nombre, descripcion, recurso1, recurso2, tiempo, inv1, inv2, inv3, inv4, inv5, inv6, inv7, inv8, inv9, inv10, inv11, inv12, inv13, ataque, defensa, escudo, carga, autodestruccion, atraviesa_escudos, camuflaje, atraviesa_stargate FROM unidades WHERE raza=\''.$_SESSION["raza"].'\' AND tipo=\'tierra\'') or die(mysql_error());
$query_cons_pend = mysql_query('SELECT id, cantidad, horafinalizar, unidad FROM cons_pend WHERE jugador=\''.$_SESSION['nick'].'\' AND cancelado=\'0\' AND planeta=\''.$_SESSION["planeta"].'\' AND tipo=\'tropas\'')or die(mysql_error());
$cons_pend = mysql_fetch_array($query_cons_pend);

while($nuevas_tropas_dat = mysql_fetch_array($query_nuevas_tropas)){
	if($nuevas_tropas_dat["inv1"]<= $jugador_dat["inv1"] && $nuevas_tropas_dat["inv2"]<= $jugador_dat["inv2"] && $nuevas_tropas_dat["inv3"]<= $jugador_dat["inv3"] && $nuevas_tropas_dat["inv4"]<= $jugador_dat["inv4"] && $nuevas_tropas_dat["inv5"]<= $jugador_dat["inv5"] && $nuevas_tropas_dat["inv6"]<= $jugador_dat["inv6"] && $nuevas_tropas_dat["inv7"]<= $jugador_dat["inv7"] && $nuevas_tropas_dat["inv8"]<= $jugador_dat["inv8"] && $nuevas_tropas_dat["inv9"]<= $jugador_dat["inv9"] && $nuevas_tropas_dat["inv10"]<= $jugador_dat["inv10"] && $nuevas_tropas_dat["inv11"]<= $jugador_dat["inv11"] && $nuevas_tropas_dat["inv12"]<= $jugador_dat["inv12"] && $nuevas_tropas_dat["inv13"]<= $jugador_dat["inv13"] ){
		echo '<div id="trop">';
		echo '<p>';
		printf("%s",$nuevas_tropas_dat["nombre"]);
		echo '</p>';
		echo '<div id="botonmejora">';
		if ($jugador_dat["recurso1"] >= $nuevas_tropas_dat["recurso1"] && $jugador_dat["recurso2"] >= $nuevas_tropas_dat["recurso2"] && empty($cons_pend[0])){
			echo '<form action="juego-index.php?control=tropas" method="post" name="nuevas_tropas">';
			echo '<p><input type="text" name="cantidad" onkeypress="return justNumbers(event);"></p>';
			echo '<input type="hidden" name="recurso1" value="'.$nuevas_tropas_dat["recurso1"].'">';
			echo '<input type="hidden" name="recurso2" value="'.$nuevas_tropas_dat["recurso2"].'">';
			echo '<input type="hidden" name="tiempo" value="'.$nuevas_tropas_dat["tiempo"].'">';
			echo '<input type="hidden" name="nombre" value="'.$nuevas_tropas_dat["nombre"].'">';
			echo '<p><input type="submit" value="crear" name="crear_tropa"></p>';
			echo '</form>';
		}else if(!empty($cons_pend[0]) && $nuevas_tropas_dat["nombre"] == $cons_pend["unidad"]){
			$tiempo_restante = $cons_pend["horafinalizar"] - time();
			?>
    			<script type="text/javascript" language="JavaScript"> 
				var tiempo = <?php echo $tiempo_restante; ?>;
				function contador(){
					if(tiempo >= 86400){
						var dias =  Math.floor(tiempo / 86400);
						var horas = Math.floor(tiempo / 3600- (dias *24));
						var minutos = tiempo/60 - (dias * 24 * 60);
						var minutos = Math.floor(minutos);
						var segundos = tiempo % 60;
						if(dias > 1){
							document.formulario.reloj.value=dias + " dias "+ horas + ":" +minutos +":"+ segundos;
						}else{
							document.formulario.reloj.value=dias + " dia "+ horas + ":" +minutos +":"+ segundos;
						}
		
					}else if (tiempo >= 3600){
						var horas = Math.floor(tiempo / 3600);
						var minutos = tiempo/60 - (horas * 60);
						var minutos = Math.floor(minutos);
						var segundos = tiempo % 60;
						document.formulario.reloj.value= horas + ":" +minutos +":"+ segundos;
					}else if (tiempo >= 60){
						var minutos = tiempo/60;
						var minutos = Math.floor(minutos);
						var segundos = tiempo % 60;
						document.formulario.reloj.value= minutos +":"+ segundos;
					}else if(tiempo < 60){
						var segundos = tiempo;
						document.formulario.reloj.value= segundos;
					}
					tiempo--;
	  				if (tiempo < 0){
						location.reload();
					}
				}
				window.onload = contador;

				setInterval("contador()",1000);  
				</script> 
    		<?php
			printf("%s", $cons_pend["cantidad"]);
			echo '<form name="formulario"><input type="text" name="reloj" value="" size="25" style="background-color:#006; color:#FFF; border : 0px ; text-align : center"></form>';
			echo '<form action="juego-index.php?control=tropas" method="post">';
			echo '<input type="submit" value="Cancelar" name="cancelar_tropas">';
			echo '</form>';
			
		}else if ($jugador_dat["recurso1"] >= $nuevas_tropas_dat["recurso1"] && $jugador_dat["recurso2"] >= $nuevas_tropas_dat["recurso2"]){
			echo '<p>Ya tienes una construcción en curso</p>';
		}else{
			echo '<p>No tienes recursos suficientes</p>';
		}
	   	echo '</div>';
		echo '<p>';
		printf("%s",$nuevas_tropas_dat["descripcion"]);
		echo '</p>';
		echo '<p>';
		printf("recurso1: %s",$nuevas_tropas_dat["recurso1"]);
		echo '</p>';
		echo '<p>';
		printf("recurso2: %s",$nuevas_tropas_dat["recurso2"]);
		echo '</p>';
		echo '<p>';
		echo 'Tiempo: ';
		convierteseg($nuevas_tropas_dat["tiempo"]);
		echo '</p>';
		echo '</div>';
	}
}
?>
</div>
<div id="requisitos">
<?php
$query_nuevas_tropas = mysql_query('SELECT nombre, descripcion, recurso1, recurso2, tiempo, inv1, inv2, inv3, inv4, inv5, inv6, inv7, inv8, inv9, inv10, inv11, inv12, inv13, ataque, defensa, escudo, carga, autodestruccion, atraviesa_escudos, camuflaje, atraviesa_stargate FROM unidades WHERE raza=\''.$_SESSION["raza"].'\' AND tipo=\'tierra\'') or die(mysql_error());
$query_inves_raza = mysql_query('SELECT nombre, numero FROM investigaciones WHERE raza=\''.$_SESSION["raza"].'\'') or die(mysql_error());

$array_nombres_inves = array();
while ($inves_raza = mysql_fetch_array($query_inves_raza)){
	$array_nombres_inves[$inves_raza["numero"]] = $inves_raza["nombre"];
}
while($nuevas_tropas_dat = mysql_fetch_array($query_nuevas_tropas)){
	echo '<div id="trop">';
	echo '<div id="botonmejora">';
	for($i = 1; $i <= 13;$i++){
		if ($nuevas_tropas_dat["inv".$i] > 0){
			if($jugador_dat["inv".$i] >= $nuevas_tropas_dat["inv".$i]){
				echo '<div id="requcumplido">';
			}else {
				echo '<div id="requnocumplido">';
			}
			echo $array_nombres_inves[$i].'&nbsp; &nbsp;&nbsp;&nbsp;';
			echo $nuevas_tropas_dat["inv".$i];
			echo '</div>';
		}
	}
	echo '</div>';
	echo '<p>';
	printf("%s",$nuevas_tropas_dat["nombre"]);
	echo '<p>';
	echo '</div>';
}
?>
</div>
</div>
<?php
}else if($_GET["control"] == "naves"){
?>
	<div id="principal">
<!-- Script encargado de separar las tropas existentes de las nuevas -->
<script>
function cambio(numero){
	switch (numero){
		case 1:
			document.getElementById("actuales").style.display = 'block';
			document.getElementById("nuevas").style.display = 'none';
			document.getElementById("requisitos").style.display = 'none';
		break;	
		case 2:
			document.getElementById("actuales").style.display = 'none';
			document.getElementById("nuevas").style.display = 'block';
			document.getElementById("requisitos").style.display = 'none';
		break;
		case 3:
			document.getElementById("actuales").style.display = 'none';
			document.getElementById("nuevas").style.display = 'none';
			document.getElementById("requisitos").style.display = 'block';
		break;
	}
}
</script>
<div id="cambio">
<table>
<tr>
<td><a onclick="cambio(1)" href="#">Naves en el Planeta</a></td>
<td><a onclick="cambio(2)" href="#">Construir Naves</a></td>
<td><a onclick="cambio(3)" href="#">Requisitos</a></td>
</tr>
</table>
</div>
<div id="actuales" >
<?php
$query_naves = mysql_query('SELECT * FROM existencias_naves WHERE planetaactual=\''.$_SESSION["planeta"].'\'') or die (mysql_error());
$datos_naves = mysql_fetch_array($query_naves);
if(empty($datos_naves)){
	echo "<p>No tienes naves en este planeta</p>";
}else{
$columnas = array_keys($datos_naves);
$query_naves_actuales = mysql_query('SELECT ataque, defensa, escudo, carga, autodestruccion, atraviesa_escudos, camuflaje, atraviesa_stargate FROM unidades WHERE tipo=\'caza\' OR tipo=\'crucero\' OR tipo=\'nodriza\'');
$naves_actuales = mysql_fetch_array($query_naves_actuales);
	for($i = 0;$i < mysql_num_fields($query_naves);$i++){
		if($i >= 3){
			if ($datos_naves[$i] != NULL){
				echo '<div id="trop">';
				echo " ".$columnas[($i*2)+1].":";
				echo " ".$datos_naves[$i];
				echo '</div>';
			}
		}
	}
}
?>
</div>
<div id="nuevas">
<script>
function justNumbers(e) {
var keynum = window.event ? window.event.keyCode : e.which;
if ( keynum == 8 ) return true;
return /\d/.test(String.fromCharCode(keynum));
}
</script>
<?php
$query_nuevas_naves = mysql_query('SELECT nombre, descripcion, recurso1, recurso2, tiempo, inv1, inv2, inv3, inv4, inv5, inv6, inv7, inv8, inv9, inv10, inv11, inv12, inv13, ataque, defensa, escudo, carga, autodestruccion, atraviesa_escudos, camuflaje, atraviesa_stargate FROM unidades WHERE raza=\''.$_SESSION["raza"].'\' AND (tipo=\'caza\' OR tipo=\'crucero\' OR tipo=\'nodriza\')') or die(mysql_error());
$query_cons_pend = mysql_query('SELECT id, cantidad, horafinalizar, unidad FROM cons_pend WHERE jugador=\''.$_SESSION['nick'].'\' AND cancelado=\'0\' AND planeta=\''.$_SESSION["planeta"].'\' AND tipo=\'naves\'')or die(mysql_error());
$cons_pend = mysql_fetch_array($query_cons_pend);


while($nuevas_naves_dat = mysql_fetch_array($query_nuevas_naves)){
	if($nuevas_naves_dat["inv1"]<= $jugador_dat["inv1"] && $nuevas_naves_dat["inv2"]<= $jugador_dat["inv2"] && $nuevas_naves_dat["inv3"]<= $jugador_dat["inv3"] && $nuevas_naves_dat["inv4"]<= $jugador_dat["inv4"] && $nuevas_naves_dat["inv5"]<= $jugador_dat["inv5"] && $nuevas_naves_dat["inv6"]<= $jugador_dat["inv6"] && $nuevas_naves_dat["inv7"]<= $jugador_dat["inv7"] && $nuevas_naves_dat["inv8"]<= $jugador_dat["inv8"] && $nuevas_naves_dat["inv9"]<= $jugador_dat["inv9"] && $nuevas_naves_dat["inv10"]<= $jugador_dat["inv10"] && $nuevas_naves_dat["inv11"]<= $jugador_dat["inv11"] && $nuevas_naves_dat["inv12"]<= $jugador_dat["inv12"] && $nuevas_naves_dat["inv13"]<= $jugador_dat["inv13"] ){
		echo '<div id="trop">';
		echo '<p>';
		printf("%s",$nuevas_naves_dat["nombre"]);
		echo '</p>';
		echo '<div id="botonmejora">';
		if ($jugador_dat["recurso1"] >= $nuevas_naves_dat["recurso1"] && $jugador_dat["recurso2"] >= $nuevas_naves_dat["recurso2"] && empty($cons_pend[0])){
			echo '<form action="juego-index.php?control=naves" method="post" name="nuevas_naves">';
			echo '<p><input type="text" name="cantidad" onkeypress="return justNumbers(event);"></p>';
			echo '<input type="hidden" name="recurso1" value="'.$nuevas_naves_dat["recurso1"].'">';
			echo '<input type="hidden" name="recurso2" value="'.$nuevas_naves_dat["recurso2"].'">';
			echo '<input type="hidden" name="tiempo" value="'.$nuevas_naves_dat["tiempo"].'">';
			echo '<input type="hidden" name="nombre" value="'.$nuevas_naves_dat["nombre"].'">';
			echo '<p><input type="submit" value="crear" name="crear_nave"></p>';
			echo '</form>';
		}else if(!empty($cons_pend[0]) && $nuevas_naves_dat["nombre"] == $cons_pend["unidad"]){
			$tiempo_restante = $cons_pend["horafinalizar"] - time();
			?>
    			<script type="text/javascript" language="JavaScript"> 
				var tiempo = <?php echo $tiempo_restante; ?>;
				function contador(){
					if(tiempo >= 86400){
						var dias =  Math.floor(tiempo / 86400);
						var horas = Math.floor(tiempo / 3600- (dias *24));
						var minutos = tiempo/60 - (dias * 24 * 60);
						var minutos = Math.floor(minutos);
						var segundos = tiempo % 60;
						if(dias > 1){
							document.formulario.reloj.value=dias + " dias "+ horas + ":" +minutos +":"+ segundos;
						}else{
							document.formulario.reloj.value=dias + " dia "+ horas + ":" +minutos +":"+ segundos;
						}
		
					}else if (tiempo >= 3600){
						var horas = Math.floor(tiempo / 3600);
						var minutos = tiempo/60 - (horas * 60);
						var minutos = Math.floor(minutos);
						var segundos = tiempo % 60;
						document.formulario.reloj.value= horas + ":" +minutos +":"+ segundos;
					}else if (tiempo >= 60){
						var minutos = tiempo/60;
						var minutos = Math.floor(minutos);
						var segundos = tiempo % 60;
						document.formulario.reloj.value= minutos +":"+ segundos;
					}else if(tiempo < 60){
						var segundos = tiempo;
						document.formulario.reloj.value= segundos;
					}
					tiempo--;
	  				if (tiempo < 0){
						location.reload();
					}
				}
				window.onload = contador;

				setInterval("contador()",1000);  
				</script> 
    		<?php
			printf("%s", $cons_pend["cantidad"]);
			echo '<form name="formulario"><input type="text" name="reloj" value="" size="25" style="background-color:#006; color:#FFF; border : 0px ; text-align : center"></form>';
			echo '<form action="juego-index.php?control=naves" method="post">';
			echo '<input type="submit" value="Cancelar" name="cancelar_naves">';
			echo '</form>';
			
		}else if ($jugador_dat["recurso1"] >= $nuevas_naves_dat["recurso1"] && $jugador_dat["recurso2"] >= $nuevas_naves_dat["recurso2"]){
			echo '<p>Ya tienes una construcción en curso</p>';
		}else{
			echo '<p>No tienes recursos suficientes</p>';
		}
	   	echo '</div>';
		echo '<p>';
		printf("%s",$nuevas_naves_dat["descripcion"]);
		echo '</p>';
		echo '<p>';
		printf("recurso1: %s",$nuevas_naves_dat["recurso1"]);
		echo '</p>';
		echo '<p>';
		printf("recurso2: %s",$nuevas_naves_dat["recurso2"]);
		echo '</p>';
		echo '<p>';
		echo 'Tiempo: ';
		convierteseg($nuevas_naves_dat["tiempo"]);
		echo '</p>';
		echo '</div>';
	}
}
?>
</div>
<div id="requisitos">
<?php
$query_nuevas_naves = mysql_query('SELECT nombre, descripcion, recurso1, recurso2, tiempo, inv1, inv2, inv3, inv4, inv5, inv6, inv7, inv8, inv9, inv10, inv11, inv12, inv13, ataque, defensa, escudo, carga, autodestruccion, atraviesa_escudos, camuflaje, atraviesa_stargate FROM unidades WHERE raza=\''.$_SESSION["raza"].'\' AND (tipo=\'caza\' OR tipo=\'crucero\' OR tipo=\'nodriza\')') or die(mysql_error());
$query_inves_raza = mysql_query('SELECT nombre, numero FROM investigaciones WHERE raza=\''.$_SESSION["raza"].'\'') or die(mysql_error());

$array_nombres_inves = array();
while ($inves_raza = mysql_fetch_array($query_inves_raza)){
	$array_nombres_inves[$inves_raza["numero"]] = $inves_raza["nombre"];
}
while($nuevas_naves_dat = mysql_fetch_array($query_nuevas_naves)){
	echo '<div id="trop">';
	echo '<div id="botonmejora">';
	for($i = 1; $i <= 13;$i++){
		if ($nuevas_naves_dat["inv".$i] > 0){
			if($jugador_dat["inv".$i] >= $nuevas_naves_dat["inv".$i]){
				echo '<div id="requcumplido">';
			}else {
				echo '<div id="requnocumplido">';
			}
			echo $array_nombres_inves[$i].'&nbsp; &nbsp;&nbsp;&nbsp;';
			echo $nuevas_naves_dat["inv".$i];
			echo '</div>';
		}
	}
	echo '</div>';
	echo '<p>';
	printf("%s",$nuevas_naves_dat["nombre"]);
	echo '<p>';
	echo '</div>';
}
?>
</div>
</div>
<?php
}else if($_GET["control"] == "defensas"){
?>
	<div id="principal">
<!-- Script encargado de separar las tropas existentes de las nuevas -->
<script>
function cambio(numero){
	switch (numero){
		case 1:
			document.getElementById("actuales").style.display = 'block';
			document.getElementById("nuevas").style.display = 'none';
			document.getElementById("requisitos").style.display = 'none';
		break;	
		case 2:
			document.getElementById("actuales").style.display = 'none';
			document.getElementById("nuevas").style.display = 'block';
			document.getElementById("requisitos").style.display = 'none';
		break;
		case 3:
			document.getElementById("actuales").style.display = 'none';
			document.getElementById("nuevas").style.display = 'none';
			document.getElementById("requisitos").style.display = 'block';
		break;
	}
}
</script>
<div id="cambio">
<table width="433">
<tr>
<td width="169" height="40"><a onclick="cambio(1)" href="#">Defensas en el Planeta</a></td>
<td width="140"><a onclick="cambio(2)" href="#">Construir Defensas</a></td>
<td width="108"><a onclick="cambio(3)" href="#">Requisitos</a></td>
</tr>
</table>
</div>
<div id="actuales" >
<?php
$query_defensas = mysql_query('SELECT * FROM existencias_defensas WHERE planetaactual=\''.$_SESSION["planeta"].'\'') or die (mysql_error());
$datos_defensas = mysql_fetch_array($query_defensas);
if(empty($datos_defensas)){
	echo "<p>No tienes defensas en este planeta</p>";
}else{
$columnas = array_keys($datos_defensas);
$query_defensas_actuales = mysql_query('SELECT ataque, defensa, escudo, carga, autodestruccion, atraviesa_escudos, camuflaje, atraviesa_stargate FROM unidades WHERE tipo=\'defensa\' OR tipo=\'satelite_defensa\' OR tipo=\'defensa_aerea\'');
$defensas_actuales = mysql_fetch_array($query_defensas_actuales);
	for($i = 0;$i < mysql_num_fields($query_defensas);$i++){
		if($i >= 3){
			if ($datos_defensas[$i] != NULL){
				echo '<div id="trop">';
				echo " ".$columnas[($i*2)+1].":";
				echo " ".$datos_defensas[$i];
				echo '</div>';
			}
		}
	}
}
?>
</div>
<div id="nuevas">
<script>
function justNumbers(e) {
var keynum = window.event ? window.event.keyCode : e.which;
if ( keynum == 8 ) return true;
return /\d/.test(String.fromCharCode(keynum));
}
</script>
<?php
$query_nuevas_defensas = mysql_query('SELECT nombre, descripcion, recurso1, recurso2, tiempo, inv1, inv2, inv3, inv4, inv5, inv6, inv7, inv8, inv9, inv10, inv11, inv12, inv13, ataque, defensa, escudo, carga, autodestruccion, atraviesa_escudos, camuflaje, atraviesa_stargate FROM unidades WHERE raza=\''.$_SESSION["raza"].'\' AND (tipo=\'defensa\' OR tipo=\'satelite_defensa\' OR tipo=\'defensa_aerea\')') or die(mysql_error());
$query_cons_pend = mysql_query('SELECT id, cantidad, horafinalizar, unidad FROM cons_pend WHERE jugador=\''.$_SESSION['nick'].'\' AND cancelado=\'0\' AND planeta=\''.$_SESSION["planeta"].'\' AND tipo=\'defensas\'')or die(mysql_error());
$cons_pend = mysql_fetch_array($query_cons_pend);

while($nuevas_defensas_dat = mysql_fetch_array($query_nuevas_defensas)){
	if($nuevas_defensas_dat["inv1"]<= $jugador_dat["inv1"] && $nuevas_defensas_dat["inv2"]<= $jugador_dat["inv2"] && $nuevas_defensas_dat["inv3"]<= $jugador_dat["inv3"] && $nuevas_defensas_dat["inv4"]<= $jugador_dat["inv4"] && $nuevas_defensas_dat["inv5"]<= $jugador_dat["inv5"] && $nuevas_defensas_dat["inv6"]<= $jugador_dat["inv6"] && $nuevas_defensas_dat["inv7"]<= $jugador_dat["inv7"] && $nuevas_defensas_dat["inv8"]<= $jugador_dat["inv8"] && $nuevas_defensas_dat["inv9"]<= $jugador_dat["inv9"] && $nuevas_defensas_dat["inv10"]<= $jugador_dat["inv10"] && $nuevas_defensas_dat["inv11"]<= $jugador_dat["inv11"] && $nuevas_defensas_dat["inv12"]<= $jugador_dat["inv12"] && $nuevas_defensas_dat["inv13"]<= $jugador_dat["inv13"] ){
		echo '<div id="trop">';
		echo '<p>';
		printf("%s",$nuevas_defensas_dat["nombre"]);
		echo '</p>';
		echo '<div id="botonmejora">';
		if ($jugador_dat["recurso1"] >= $nuevas_defensas_dat["recurso1"] && $jugador_dat["recurso2"] >= $nuevas_defensas_dat["recurso2"] && empty($cons_pend[0])){
		echo '<form action="juego-index.php?control=defensas" method="post" name="nuevas_defensas">';
		echo '<p><input type="text" name="cantidad" onkeypress="return justNumbers(event);"></p>';
		echo '<input type="hidden" name="recurso1" value="'.$nuevas_defensas_dat["recurso1"].'">';
		echo '<input type="hidden" name="recurso2" value="'.$nuevas_defensas_dat["recurso2"].'">';
		echo '<input type="hidden" name="tiempo" value="'.$nuevas_defensas_dat["tiempo"].'">';
		echo '<input type="hidden" name="nombre" value="'.$nuevas_defensas_dat["nombre"].'">';
		echo '<p><input type="submit" value="crear" name="crear_defensa"></p>';
		echo '</form>';
		}else if(!empty($cons_pend[0]) && $nuevas_defensas_dat["nombre"] == $cons_pend["unidad"]){
			$tiempo_restante = $cons_pend["horafinalizar"] - time();
			?>
    			<script type="text/javascript" language="JavaScript"> 
				var tiempo = <?php echo $tiempo_restante; ?>;
				function contador(){
					if(tiempo >= 86400){
						var dias =  Math.floor(tiempo / 86400);
						var horas = Math.floor(tiempo / 3600- (dias *24));
						var minutos = tiempo/60 - (dias * 24 * 60);
						var minutos = Math.floor(minutos);
						var segundos = tiempo % 60;
						if(dias > 1){
							document.formulario.reloj.value=dias + " dias "+ horas + ":" +minutos +":"+ segundos;
						}else{
							document.formulario.reloj.value=dias + " dia "+ horas + ":" +minutos +":"+ segundos;
						}
		
					}else if (tiempo >= 3600){
						var horas = Math.floor(tiempo / 3600);
						var minutos = tiempo/60 - (horas * 60);
						var minutos = Math.floor(minutos);
						var segundos = tiempo % 60;
						document.formulario.reloj.value= horas + ":" +minutos +":"+ segundos;
					}else if (tiempo >= 60){
						var minutos = tiempo/60;
						var minutos = Math.floor(minutos);
						var segundos = tiempo % 60;
						document.formulario.reloj.value= minutos +":"+ segundos;
					}else if(tiempo < 60){
						var segundos = tiempo;
						document.formulario.reloj.value= segundos;
					}
					tiempo--;
	  				if (tiempo < 0){
						location.reload();
					}
				}
				window.onload = contador;

				setInterval("contador()",1000);  
				</script> 
    		<?php
			printf("%s", $cons_pend["cantidad"]);
			echo '<form name="formulario"><input type="text" name="reloj" value="" size="25" style="background-color:#006; color:#FFF; border : 0px ; text-align : center"></form>';
			echo '<form action="juego-index.php?control=defensas" method="post">';
			echo '<input type="submit" value="Cancelar" name="cancelar_defensas">';
			echo '</form>';
			
		}else if ($jugador_dat["recurso1"] >= $nuevas_defensas_dat["recurso1"] && $jugador_dat["recurso2"] >= $nuevas_defensas_dat["recurso2"]){
			echo '<p>Ya tienes una construcción en curso</p>';
		}else{
			echo '<p>No tienes recursos suficientes</p>';
		}
	   	echo '</div>';
		echo '<p>';
		printf("%s",$nuevas_defensas_dat["descripcion"]);
		echo '</p>';
		echo '<p>';
		printf("recurso1: %s",$nuevas_defensas_dat["recurso1"]);
		echo '</p>';
		echo '<p>';
		printf("recurso2: %s",$nuevas_defensas_dat["recurso2"]);
		echo '</p>';
		echo '<p>';
		echo 'Tiempo: ';
		convierteseg($nuevas_defensas_dat["tiempo"]);
		echo '</p>';
		echo '</div>';
	}
}
?>
</div>
<div id="requisitos">
<?php
$query_nuevas_defensas = mysql_query('SELECT nombre, descripcion, recurso1, recurso2, tiempo, inv1, inv2, inv3, inv4, inv5, inv6, inv7, inv8, inv9, inv10, inv11, inv12, inv13, ataque, defensa, escudo, carga, autodestruccion, atraviesa_escudos, camuflaje, atraviesa_stargate FROM unidades WHERE raza=\''.$_SESSION["raza"].'\' AND (tipo=\'defensa\' OR tipo=\'satelite_defensa\' OR tipo=\'defensa_aerea\')') or die(mysql_error());
$query_inves_raza = mysql_query('SELECT nombre, numero FROM investigaciones WHERE raza=\''.$_SESSION["raza"].'\'') or die(mysql_error());

$array_nombres_inves = array();
while ($inves_raza = mysql_fetch_array($query_inves_raza)){
	$array_nombres_inves[$inves_raza["numero"]] = $inves_raza["nombre"];
}
while($nuevas_defensas_dat = mysql_fetch_array($query_nuevas_defensas)){
	echo '<div id="trop">';
	echo '<div id="botonmejora">';
	for($i = 1; $i <= 13;$i++){
		if ($nuevas_defensas_dat["inv".$i] > 0){
			if($jugador_dat["inv".$i] >= $nuevas_defensas_dat["inv".$i]){
				echo '<div id="requcumplido">';
			}else {
				echo '<div id="requnocumplido">';
			}
			echo $array_nombres_inves[$i].'&nbsp; &nbsp;&nbsp;&nbsp;';
			echo $nuevas_defensas_dat["inv".$i];
			echo '</div>';
		}
	}
	echo '</div>';
	echo '<p>';
	printf("%s",$nuevas_defensas_dat["nombre"]);
	echo '<p>';
	echo '</div>';
}
?>
</div>
</div>
<?php
}else if($_GET["control"] == "galaxia"){
?>
	<div id="principal">
<p>galaxia.</p>
</div>
<?php
}else if($_GET["control"] == "alianza"){
?>
	<div id="principal">
<p>alianza.</p>
</div>
<?php
}else if($_GET["control"] == "mensajes"){
?>
	<div id="principal">
    <!-- Script encargado de separar los mensajes recibidos del apartado para enviar -->
<script>
function cambio(numero){
	switch (numero){
		case 1:
			document.getElementById("listamensaje").style.display = 'block';
			document.getElementById("nuevomensaje").style.display = 'none';
		break;	
		case 2:
			document.getElementById("listamensaje").style.display = 'none';
			document.getElementById("nuevomensaje").style.display = 'block';
		break;
	}
}
</script>
    <div id="cambio">
    <table width="347">
		<tr>
			<td width="167"><a onclick="cambio(1)" href="#">Mensajes Recibidos</a></td>
			<td width="168"><a onclick="cambio(2)" href="#">Enviar Nuevos</a></td>
		</tr>
	</table>
    </div>
    <div id="listamensaje">
    <table>
    <tr>
    <td>Asunto</td>
    <td>Enviado Por</td>
    <td>Hora</td>
    </tr>
    <?php
	$query_mensajes = mysql_query('SELECT id, de, hora, asunto, leido FROM mensajes WHERE para = \''.$_SESSION["nick"].'\' ORDER BY id DESC') or die(mysql_error());
	if (isset($pagina)){
		$total = mysql_num_rows($query_mensajes);
		$numamostrar = 15;
		$numpaginas = ceil($total / $numamostrar);
		$limitesuperior = $pagina * $numamostrar;
		$limiteinferior = $limitesuperior - $numamostrar;
	}else{
		$pagina = 1;
		$total = mysql_num_rows($query_mensajes);
		$numamostrar = 15;
		$numpaginas = ceil($total / $numamostrar);
		$limitesuperior = $pagina * $numamostrar;
		$limiteinferior = $limitesuperior - $numamostrar;
		
	}
	$j = 0;
	while($mensaje = mysql_fetch_array($query_mensajes)){
		if(($j>=$limiteinferior) && ($j<$limitesuperior)){
			?>
<tr>

<td><a href="juego-index.php?control=leer_mensaje&id=<?php echo $mensaje["id"] ?>"><?php echo $mensaje["asunto"];?></a></td>
<td><a href="juego-index.php?control=leer_mensaje&id=<?php echo $mensaje["id"] ?>"><?php echo $mensaje["de"];?></a></td>
<td><a href="juego-index.php?control=leer_mensaje&id=<?php echo $mensaje["id"] ?>"><?php echo $mensaje["hora"];?></a></td>

</tr>
<?php
		}
		$j++;
	}
	?>
    </table>
    </div>
    <div id="nuevomensaje">
    <form action="juego-index.php?control=mensajes" method="post" name="mensaje_nuevo">
    <p>Para: <input type="text" name="destinatario" /></p>
    <p>Asunto: <input type="text" name="asunto"/></p>
    <p><textarea name="cuerpo"></textarea></p>
    <input type="hidden" name="hora" value="<?php echo time();?>"/>
    <input type="hidden" name="remitente" value="<?php echo $_SESSION["nick"]; ?>"/>
    <p><input type="submit" name="enviar_mensaje" value="Enviar" /></p>
    </form>
    </div>
	</div>
    <?php
}else if($_GET["control"] == "leer_mensaje"){
	$id = htmlentities($_GET["id"]);
	$query_leer_mensaje = mysql_query('SELECT de, hora, asunto, mensaje FROM mensajes WHERE id=\''.$id.'\' AND para =\''.$_SESSION["nick"].'\'') or die(mysql_error());
	$leer_mensajes = mysql_fetch_array($query_leer_mensaje) or die(mysql_error());
	mysql_query('UPDATE mensajes SET leido=\'1\' WHERE id=\''.$id.'\'') or die(mysql_error());
	?>
	<div id="principal">
     <table>
    <tr>
    <td>De</td>
    <td><?php echo $leer_mensajes["de"]; ?></td>
    </tr>
    <tr>
    <td>Asunto</td>
    <td><?php echo $leer_mensajes["asunto"]; ?></td>
    </tr>
    <tr>
    <td><?php echo $leer_mensajes["mensaje"]; ?></td>
    
    </tr>
    </table>
	</div>
	<?php
}else if($_GET["control"] == "ranking"){
?>
	<div id="principal">
<p>ranking.</p>
</div>
<?php
}else if($_GET["control"] == "opciones"){
?>
	<div id="principal">
<p>opciones.</p>
</div>
<?php
}
?>
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
}else{
	echo '<SCRIPT LANGUAJE="javascript">';
	echo 'location.href = "index.php";';
	echo '</SCRIPT>';
}