<?php

//conecta a db

$conecta = mysql_pconnect('localhost','USER','PASSWORD') or die ('Ha fallado la conexi&oacute;n:'.mysql_error());
$seleciona_bd = mysql_select_db('DATABASE') or die ('Error al seleccionar la base de datos.'.mysql_error());

//bucle que ejecuta continuamente el motor
while(true){

//comprueba si se ha perdido la conexión con la bd, en cuyo caso la reestablece
if( !mysql_ping($conecta) ){
	
	$conecta = mysql_pconnect('localhost','USER','PASSWORD') or die ('Ha fallado la conexi&oacute;n:'.mysql_error());
	$seleciona_bd = mysql_select_db('DATABASE') or die ('Error al seleccionar la base de datos.'.mysql_error());
}
$query_mov_pend = mysql_query('SELECT id, horallegada, cancelado FROM mov_pend ORDER BY horallegada ASC LIMIT 1')or die(mysql_error());
$mov_pend = mysql_fetch_array($query_mov_pend);
$query_cons_pend = mysql_query('SELECT id, horafinalizar, cancelado FROM cons_pend ORDER BY horafinalizar ASC LIMIT 1')or die(mysql_error());
$cons_pend = mysql_fetch_array($query_cons_pend);
$query_inves_pend = mysql_query('SELECT id, horafinalizar, cancelado FROM inves_pend ORDER BY horafinalizar ASC LIMIT 1')or die(mysql_error());
$inves_pend = mysql_fetch_array($query_inves_pend);


//FASE 1: Si hay movimientos pendientes, comprueba el tiempo de la tarea que menos tarda para entrar en sleep
//en caso de que no haya nada entra en sleep 30 segundos

$tiempo_mov = 9999999999999;
$tiempo_cons = 9999999999999;
$tiempo_inves = 9999999999999;
if (!empty($mov_pend)){
	if ($tiempo_mov > $mov_pend["horallegada"]){
		if($mov_pend["cancelado"] == 0){
			$tiempo_mov = $mov_pend["horallegada"];
		}else{
			mysql_query('DELETE FROM mov_pend WHERE id = \''.$mov_pend["id"].'\'') or die(mysql_error());
			continue;
		}
	}

	
}else{
	
}
if(!empty($cons_pend)){
	if ($tiempo_cons > $cons_pend["horafinalizar"]){
		if($cons_pend["cancelado"] == 0){
			$tiempo_cons = $cons_pend["horafinalizar"];
		}else{
			mysql_query('DELETE FROM cons_pend WHERE id = \''.$cons_pend["id"].'\'') or die(mysql_error());
			continue;
		}
	}
	
	
}
if(!empty($inves_pend)){
	if ($tiempo_inves > $inves_pend["horafinalizar"]){
		if($inves_pend["cancelado"] == 0){
			$tiempo_inves = $inves_pend["horafinalizar"];
		}else{
			mysql_query('DELETE FROM inves_pend WHERE id = \''.$inves_pend["id"].'\'') or die(mysql_error());
			continue;
		}
	}
	
}
if(empty($mov_pend) && empty($cons_pend) && empty($inves_pend)){
	//no hay tareas, entra en descanso para no consumir recursos
	sleep(5);
	continue;
}

//FASE 1.1 decide cual es la tarea más próxima
if ($tiempo_mov <= $tiempo_cons && $tiempo_mov <= $tiempo_inves ){
	$tiempo_tarea = $tiempo_mov;
	$ejecuta = "movimiento";
}else if ($tiempo_cons <= $tiempo_inves){
	$tiempo_tarea = $tiempo_cons;
	$ejecuta = "construccion";
}else {
	$tiempo_tarea = $tiempo_inves;
	$ejecuta = "investigacion";
}

//FASE 2: entrar en Sleep el tiempo necesario hasta que haya que ejecutar la siguiente tarea(en caso de que sea un tiempo inferior a 58 segundos
//Obtengo el tiempo actual
$tiempo_actual = time();


//calculo si el tiempo es superior o inferior a 50 segundos y entro en sleep el tiempo necesario
if (($tiempo_actual + 5) < $tiempo_tarea){
	sleep(5);
	continue;
}else if(($tiempo_actual + 5) > $tiempo_tarea){
	$tiemposleep = $tiempo_tarea - $tiempo_actual;
	if($tiemposleep != 0 && $tiemposleep > 0){
		sleep($tiemposleep);
	}
}


//FASE 3: ejecutar la tarea dependiendo del tipo
if($ejecuta == "movimiento"){
}else if($ejecuta == "construccion"){
	$query_actual = mysql_query('SELECT cancelado, jugador, planeta, cantidad, unidad, tipo, id FROM cons_pend WHERE id=\''.$cons_pend[0].'\' ') or die(mysql_error());
	$tarea_actual = mysql_fetch_array($query_actual);
	if ($tarea_actual[0] == 0){
		$query_exitencias = mysql_query('SELECT id, '.$tarea_actual["unidad"].' FROM existencias_'.$tarea_actual["tipo"].' WHERE dueno=\''.$tarea_actual["jugador"].'\' AND planetaactual=\''.$tarea_actual["planeta"].'\'') or die(mysql_error());
		$existencias = mysql_fetch_array($query_exitencias);
		if (empty($existencias)){
			mysql_query('INSERT INTO existencias_'.$tarea_actual["tipo"].' (planetaactual, dueno, '.$tarea_actual["unidad"].') VALUES (\''.$tarea_actual["planeta"].'\', \''.$tarea_actual["jugador"].'\', \''.$tarea_actual["cantidad"].'\')') or die(mysql_error());
		}else{
			if ($existencias[1] != NULL){
				$cantidad_total = $existencias[1] + $tarea_actual["cantidad"];
				mysql_query('UPDATE existencias_'.$tarea_actual["tipo"].' SET '.$tarea_actual["unidad"].'=\''.$cantidad_total.'\' WHERE dueno=\''.$tarea_actual["jugador"].'\' AND planetaactual=\''.$tarea_actual["planeta"].'\'') or die(mysql_error());
			}else{
				mysql_query('UPDATE existencias_'.$tarea_actual["tipo"].' SET '.$tarea_actual["unidad"].'=\''.$tarea_actual["cantidad"].'\' WHERE dueno=\''.$tarea_actual["jugador"].'\' AND planetaactual=\''.$tarea_actual["planeta"].'\'') or die(mysql_error());
			}
		}
		mysql_query('DELETE FROM cons_pend WHERE id = \''.$cons_pend["id"].'\'') or die(mysql_error());
	}else{
		mysql_query('DELETE FROM cons_pend WHERE cancelado = \'1\'') or die(mysql_error());
	}
}else if($ejecuta == "investigacion"){
	$query_actual = mysql_query('SELECT cancelado, jugador, investigacion, nivelnuevo FROM inves_pend WHERE id=\''.$inves_pend[0].'\' ') or die(mysql_error());
	$tarea_actual = mysql_fetch_array($query_actual);
	if ($tarea_actual[0] == 0){
		mysql_query('UPDATE jugadores SET inv'.$tarea_actual["investigacion"].'=\''.$tarea_actual["nivelnuevo"].'\' WHERE nick=\''.$tarea_actual["jugador"].'\'') or die(mysql_error());
		mysql_query('DELETE FROM inves_pend WHERE id = \''.$inves_pend[0].'\'') or die(mysql_error());
	}else{
		mysql_query('DELETE FROM inves_pend WHERE cancelado = \'1\'') or die(mysql_error());
	}
	
}

//FASE 4: volver a la fase 1
}
//recuerda comprobar si se ha cancelado la acción
?>

