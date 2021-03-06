<?php session_start(); 
mysql_connect('localhost','USER','PASSWORD') or die ('Ha fallado la conexi&oacute;n:'.mysql_error());
mysql_select_db('DATABASE') or die ('Error al seleccionar la base de datos.'.mysql_error());


//almacena datos sobre el universo(esto debería hacerlo al iniciar sesion y almacenarlo en cache, al igual que lo de debajo)
$universo = 0;
$query_config = mysql_query('SELECT velocidad_naves, velocidad_tropas, velocidad_defensas, velocidad_inves, velocidad_construccion FROM Config WHERE universo=\''.$universo.'\'')or die(mysql_error());
$config = mysql_fetch_array($query_config);

//almacena datos importantes sobre el usuario(esto hay que moverlo al index, justo despues de comprobar que los datos son correctos, para ejecutarlo solo una vez)
$query_jugador_dat = mysql_query('SELECT recurso1, recurso2, inv1, inv2, inv3, inv4, inv5, inv6, inv7, inv8, inv9, inv10, inv11, inv12, inv13, nick, codigoseguridad, planeta1, planeta2, planeta3, planeta4, planeta5 FROM jugadores WHERE nick =\''.$_SESSION['nick'].'\' AND codigoseguridad =\''.$_SESSION["codigoseguridad"].'\'') or die (mysql_error());
$jugador_dat = mysql_fetch_array($query_jugador_dat);
$query_energia_inicial = mysql_query('SELECT energia FROM raza WHERE nombre =\''.$_SESSION['raza'].'\'') or die (mysql_error());
$energia_inicial = mysql_fetch_row($query_energia_inicial);
$query_inves_energ = mysql_query('SELECT numero, efecenergia FROM investigaciones WHERE raza =\''.$_SESSION["raza"].'\' AND efecenergia IS NOT NULL') or die (mysql_error());
$inves_energ = mysql_fetch_row($query_inves_energ);
$query_galaxia_planeta_principal = mysql_query('SELECT galaxia FROM mapa WHERE id =\''.$jugador_dat["planeta1"].'\'') or die(mysql_error());
$galaxia_planeta_principal = mysql_fetch_row($query_galaxia_planeta_principal);
$_SESSION['galaxia'] = $galaxia_planeta_principal[0];

//calcula la cantidad de energía total
$energia_total = $energia_inicial[0];
for ($i = 0; $i < $jugador_dat[$inves_energ[0] + 1];$i++){
	$energia_total = $energia_total + $energia_total * $inves_energ[1] / 100;
}
//función que calcula el tiempo de las investigaciones a medida que aumenta el nivel
function tiempoinves($tiempo_inicial, $nivel, $config){
	
	for($i = 0; $i < $nivel; $i++){
		$tiempo_inicial = $tiempo_inicial * (1.84 * pow($nivel, -0.1201));
	}
	$tiempo_inicial= $tiempo_inicial / ($config["velocidad_inves"] / 10);
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

function imprime_unidades($array, $indice, $cantidad){
	$cadena = "";
	$indice--;
	for($i=1;$i<=$indice;$i++){
		if($cantidad[$i] != NULL){
			if($i == 1){
				$cadena = $array[$i];
			}else{
				$cadena = $cadena.', '.$array[$i];
			}
		}
	}
	return $cadena;
}

function imprime_cantidades($indice, $cantidad){
	$cadena = "";
	$indice--;
	for($i=1;$i<=$indice;$i++){
		if($cantidad[$i] > 0){
			if($i == 1){
				$cadena = $cantidad[$i];
			}else{
				$cadena = $cadena.', '.$cantidad[$i];
			}
		}
	}
	return $cadena;
}

	function set_actualizar_existencias($array_tropas, $cantidad_original, $cantidad_nueva, $indice){
		$cadena = "";
		for($i = 1;$i<$indice; $i++){
			if($cadena == ""){
				$cadena = $array_tropas[$i]."=".nulo_0($cantidad_original[$i - 1], $cantidad_nueva[$i]);
			}else{
				$cadena = $cadena.",".$array_tropas[$i]."=".($cantidad_original[$i - 1]- $cantidad_nueva[$i]);
			}
		}
		return $cadena;
	}
	
	function nulo_0($cantidad_original, $cantidad_nueva){
		if ($cantidad_original - $cantidad_nueva == 0){
			return "NULL";
		}else{
			return $cantidad_original - $cantidad_nueva;
		}
	}


	function set_licenciar($tropas, $cantidad, $indice){
		$cadena = "";
		for($i = 1;$i<$indice; $i++){
			if($i == 1){
				$cadena = $tropas[$i]."=".$cantidad[$i];
			}else{
				$cadena = $cadena.",".$tropas[$i]."=".$cantidad[$i];
			}
		}
		return $cadena;
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
	//comprueba si se ha enviado la orden de cancelar invetigacion, en cuyo caso cancela
	if (isset($_POST["cancelar_inves"])){
		mysql_query('UPDATE inves_pend SET cancelado =\'1\' WHERE jugador=\''.$_SESSION["nick"].'\' AND cancelado=\'0\'') or die(mysql_error());
	}
	
	//añade la tarea de crear tropas
	if (isset($_POST["crear_tropa"])){
		$query_cons_pend = mysql_query('SELECT id FROM cons_pend WHERE jugador=\''.$_SESSION['nick'].'\' AND cancelado=\'0\' AND planeta=\''.$_SESSION['planeta'].'\' AND tipo=\'tropas\'')or die(mysql_error());
		$cons_pend = mysql_fetch_row($query_cons_pend);
		if (empty($cons_pend[0])){
		$cancelado = 0;
		$hora_fin_cons = time() + (($_POST["tiempo"]* $_POST["cantidad"]) / $config["velocidad_construccion"] / 10);
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
		$hora_fin_cons = time() + (($_POST["tiempo"]* $_POST["cantidad"]) / $config["velocidad_construccion"] / 10);
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
		$hora_fin_cons = time() + (($_POST["tiempo"]* $_POST["cantidad"]) / $config["velocidad_construccion"] / 10);
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
	
	//comprueba si se ha cancelado algun movimiento y hace las modificaciones necesarias
	if (isset($_POST["cancelar_mov"])){
		$query_datos_mov_cancel= mysql_query('SELECT tipo, horasalida, horallegada, horavuelta, cancelado FROM mov_pend WHERE id=\''.$_POST["id_mov"].'\'') or die(mysql_error());
		$datos_cancel= mysql_fetch_array($query_datos_mov_cancel);
		//comprueba que la mision no haya sido cancelada ya 
		if($datos_cancel["cancelado"] == 0 && $datos_cancel["horavuelta"] == NULL){
			//comprueba el tipo de mision
			if ($datos_cancel["tipo"] == 1){
				//comprueba si el despliegue ya llegó
				if ( time() >= $datos_cancel["horallegada"]){
					$horavuelta = time() + ($datos_cancel["horallegada"] - $datos_cancel["horasalida"]); 
				
				}else{
					$horavuelta = time() +(time() - $datos_cancel["horasalida"]);
				}
			}
		}
		mysql_query('UPDATE mov_pend SET cancelado =\'1\', horavuelta=\''.$horavuelta.'\' WHERE id=\''.$_POST["id_mov"].'\'') or die(mysql_error());
	}
	
	//comprueba si se han enviado mensajes
	if (isset($_POST["enviar_mensaje"])){
		if($_POST["remitente"] != "" && $_POST["destinatario"] != "" && $_POST["cuerpo"] != "" && $_POST["hora"] != ""){
			$remitente = htmlentities($_POST["remitente"]);
			$destinatario = htmlentities($_POST["destinatario"]);
			$hora = htmlentities($_POST["hora"]);
			if($_POST["asunto"] == ""){
				$asunto = "Sin asunto";
			}else{
				$asunto = htmlentities($_POST["asunto"]);
			}
			$cuerpo = htmlentities($_POST["cuerpo"]);
			mysql_query('INSERT INTO mensajes (de, para, hora, asunto, mensaje) VALUES (\''.$remitente.'\', \''.$destinatario.'\',\''.$hora.'\',\''.$asunto.'\',\''.$cuerpo.'\')') or die(mysql_error());
		}
	}
	
	//comprueba si se quiere borrar un mensaje, se comprueba que sea el destinatario, lo borra y redirige a mensajes
	if($_GET["accion"] == "borrar_mensaje"){
		$id = htmlentities($_GET["id"]);
		$query_borrar = mysql_query('SELECT para FROM mensajes WHERE id=\''.$id.'\'') or die(mysql_error());
		$borrar = mysql_fetch_array($query_borrar);
		if ($_SESSION["nick"] == $borrar["para"]){
			mysql_query('DELETE FROM mensajes WHERE id=\''.$id.'\'') or die(mysql_error());
		}
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
<?php
//esto hay que moverlo fuera del código html, pero lo dejo aquí de momento para ver mensajes de error





//comprueba si se ha dado la orden de enviar tropas
	if(isset($_POST["enviar_tropas"])){
		$enviar_tropas_array = array();
		$array_cantidad_tropas = array();
		$indice = 1;
		while(isset($_POST[$indice])){
			//recupera en un array los nombres de las unidades a enviar
			$enviar_tropas_array[$indice] = htmlentities($_POST[$indice]);
			//recupera en otro array(con el mismo orden) el numero de unidades a enviar
			$array_cantidad_tropas[$indice] = htmlentities($_POST[$enviar_tropas_array[$indice]]);
			$indice++;
		}
		
		$sql_unidades = imprime_unidades($enviar_tropas_array, $indice, $array_cantidad_tropas);
		//comprueba si se ha hecho un envio de tropas con "0" unidades
		$ejecuta = false;
		for($i = 1; $i < $indice; $i++){
			if($array_cantidad_tropas[$i] > 0){
				$ejecuta = true;
			}
		}
		if ($ejecuta == true){
			//consulta a la bd si en ese planeta existen las tropas a enviar
			//(creo que hay un fallo aquí)
			$query_existe_tropa = mysql_query('SELECT '.$sql_unidades.' FROM existencias_tropas WHERE dueno=\''.$_SESSION["nick"].'\' AND planetaactual=\''.$_SESSION["planeta"].'\'') or die(mysql_error());
			$existe_tropa = mysql_fetch_array($query_existe_tropa);
			$ejecuta_tropa = true;
			//bucle que comprueba si existen suficientes unidades para enviar
			for($i = 1;$i<=$indice;$i++){
				if($existe_tropa[$i] != NULL ){
					if($array_cantidad_tropas[$indice] > $existe_tropa[$i]){
						$ejecuta_tropa= false;
					}
				}
			}
			//obtengo el id del planeta destino mediante las coordenadas
			$query_planeta_destino = mysql_query('SELECT id from mapa WHERE galaxia=\''.$_POST["galaxia"].'\' AND sector=\''.$_POST["sector"].'\' AND cuadrante=\''.$_POST["cuadrante"].'\' AND posicion=\''.$_POST["posicion"].'\'') or die(mysql_error());
			$planeta_destino = mysql_fetch_row($query_planeta_destino);
			//convierto el nombre del tipo de mision en un int(para ahorrar recursos en la bd) que luego interpretará el motor
			if($_POST["movimiento"] == "Desplegar"){
				$tipo = 1;
				$horasalida = time(); 
				//los despliegues de tropas siempre tardan 1 minuto(60 segundos)
				$horallegada = $horasalida + (60 / $config["velocidad_tropas"] / 10);
			}elseif($_POST["movimiento"] == "Explorar"){
				$tipo = 2;
				$horasalida = time(); 
				//las exploraciones de tropas siempre tardan 1 minuto(60 segundos)
				$horallegada = $horasalida + (60 / $config["velocidad_tropas"] / 10);
			}elseif($_POST["movimiento"] == "Recolectar"){
				$tipo = 3;
				$horasalida = time(); 
				//las recolecciones de tropas siempre tardan 15 minuto(900 segundos) pero por motivos de testeos lo he dejado en 30 segundos
				$horallegada = $horasalida + (900 / $config["velocidad_tropas"] / 10);
			}elseif($_POST["movimiento"] == "Establecer Base"){
				$tipo = 4;
				$horasalida = time(); 
				//los eb de tropas siempre tardan 12 horas minuto(43200 segundos) pero por motivos de testeos lo he dejado en 200 segundos
				$horallegada = $horasalida + (43200 / $config["velocidad_tropas"] / 10);
			}
			//aquí falta calcular tiempos(naves y defensas) y meterlos en la bd
			if($ejecuta_tropa){
				mysql_query('INSERT INTO mov_pend (planetaactual, planetadestino, jugador, tipo, horasalida, horallegada, '.imprime_unidades($enviar_tropas_array, $indice, $array_cantidad_tropas).') VALUES ('.$_SESSION["planeta"].', \''.$planeta_destino[0].'\',\''.$_SESSION["nick"].'\',\''.$tipo.'\', \''.$horasalida.'\', \''.$horallegada.'\', '.imprime_cantidades($indice, $array_cantidad_tropas).')') or die(mysql_error());
				mysql_query('UPDATE existencias_tropas SET '.set_actualizar_existencias($enviar_tropas_array,$existe_tropa, $array_cantidad_tropas, $indice).' WHERE dueno=\''.$_SESSION["nick"].'\' AND planetaactual=\''.$_SESSION["planeta"].'\'') or die(mysql_error());
			}
		}
	}
	//comprueba si existe la orden de licenciar tropas
	if(isset($_POST["licenciar_tropas"])){
		$licenciar_tropas_array = array();
		$array_cantidad_tropas_licenciar = array();
		$indice = 1;
		while(isset($_POST[$indice])){
			$licenciar_tropas_array[$indice] = htmlentities($_POST[$indice]);
			$array_cantidad_tropas_licenciar[$indice] = $_POST[$licenciar_tropas_array[$indice]];
			$indice++;
		}
		$query_existe_tropa_licenciar = mysql_query('SELECT '.imprime_unidades($licenciar_tropas_array, $indice, $array_cantidad_tropas_licenciar).' FROM existencias_tropas WHERE dueno=\''.$_SESSION["nick"].'\' AND planetaactual=\''.$_SESSION["planeta"].'\'') or die(mysql_error());
		$existe_tropa_licenciar = mysql_fetch_array($query_existe_tropa_licenciar);
		$ejecuta_tropa_licenciar = true;
		$array_tropas_restantes = array();
		$cantidad_tropas_vacias = 0;
		for($i = 1;$i<=$indice;$i++){
			$array_tropas_restantes[$i] = $existe_tropa_licenciar[$i-1] - $array_cantidad_tropas_licenciar[$i];
			if($array_tropas_restantes[$i] == 0){
				$cantidad_tropas_vacias++;
			}else if($array_tropas_restantes[$i] < 0){
				$array_tropas_restantes[$i] = 0;
				$cantidad_tropas_vacias++;
			}
		}
		if($indice == $cantidad_tropas_vacias){
			mysql_query('DELETE FROM existencias_tropas WHERE planetaactual=\''.$_SESSION["planeta"].'\' AND dueno=\''.$_SESSION["nick"].'\'') or die(mysql_error());
		}else{
			mysql_query('UPDATE existencias_tropas SET '.set_licenciar($licenciar_tropas_array,$array_tropas_restantes,$indice).' WHERE planetaactual=\''.$_SESSION["planeta"].'\' AND dueno=\''.$_SESSION["nick"].'\'') or die(mysql_error());
		}
	}
	

?>

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
//query para obtener los nombres de los planetas del jugador
$query_datos_planetas = mysql_query('SELECT id, nombre FROM mapa WHERE dueno=\''.$_SESSION["nick"].'\'') or die(mysql_error());
$array_id_datos_planetas = array();
$array_nombre_datos_planetas = array();
$indice = 0;
//almaceno los id y los nombres en dos arrays ordenados
while($datos_planetas = mysql_fetch_row($query_datos_planetas)){
	$array_id_datos_planetas[$indice] = $datos_planetas[0];
	$array_nombre_datos_planetas[$indice] = $datos_planetas[1];
	$indice++;
}

for($i=1;$i <= 5;$i++){
	if ($jugador_dat[$i + 16] != NULL){
		echo '<option value="'.$jugador_dat[$i + 16].'"';
		if($_SESSION["planeta"] == $jugador_dat[$i + 16]){
			echo ' selected>';
		}else{
			echo '>';
		}
		
		//recorro el primer array hasta dar con el id en cuestión para imprimir su nombre usando el otro array
		for($j = 0 ; $j < $indice; $j++){
			if ($jugador_dat[$i + 16] == $array_id_datos_planetas[$j]){
				printf("%s",$array_nombre_datos_planetas[$j]);
			}
		}
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
<p><a href="bugs.alphasgcon.hol.es">Bugs</a></p>
<p><a href="juego-index.php?control=salir">Salir</a></p>

</div>
<?php 
//aquí falta mucho por añadir
if($_GET["control"] == "inicio" || $_GET["control"] == ""){

	include("include/inicio.php");

}else if($_GET["control"] == "investiga"){

	include("include/investigacion.php");

}else if($_GET["control"] == "recursos"){
?>
	<div id="principal">
<p>recursos.</p>
</div>
<?php
}else if($_GET["control"] == "tropas"){

	include("include/tropas.php");

}else if($_GET["control"] == "naves"){

	include("include/naves.php");

}else if($_GET["control"] == "defensas"){
	
	include("include/defensas.php");
	
}else if($_GET["control"] == "galaxia"){
	
	include("include/galaxia.php");
	
}else if($_GET["control"] == "alianza"){
?>
	<div id="principal">
<p>alianza.</p>
</div>
<?php
}else if($_GET["control"] == "mensajes"){
	include("include/mensaje.php");
	
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
    <tr>
    <td><a href="juego-index.php?control=mensajes&accion=responder&destinatario=<?php echo $leer_mensajes["de"]; ?>">Responder</a></td>
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
SGconquers es un proyecto opensource y sin &aacute;nimo de lucro, cualquiera que desee ayudar es bienvenid@.		Versión:0.1.0 motor:0.1.0
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