<?php

error_reporting(E_ALL); 
ini_set('memory_limit', '64M');
//conecta a db

$conecta = mysql_connect('localhost','sgcon','gool34') or die ('Ha fallado la conexi&oacute;n:'.mysql_error());
$seleciona_bd = mysql_select_db('sgcon') or die ('Error al seleccionar la base de datos.'.mysql_error());

//consulta datos básicos(si cambian hay que reiniciar el motor para que surtan efecto)
$universo = 0;
$query_config = mysql_query('SELECT velocidad_naves, velocidad_tropas, velocidad_defensas, velocidad_inves FROM Config WHERE universo=\''.$universo.'\'')or die(mysql_error());
$config = mysql_fetch_array($query_config);


//funciones

//genera parte de una cadena sql para actualizar las existencias
function set_actualizar_existencias($datos, $datos2){
		$cadena = "";
		$columnas = array_keys($datos);
		for($i = 12;$i <= 60; $i++){
			if($datos[$i] > 0){
				//comprobamos si el valor del campo que almacena el numero de unidades es null(en cuyo caso no vale el truco de sonda_asgard= sonda_asgard +1, sino que hay establecer el valor directamente)
				if ($datos2[$columnas[($i*2)+1]] == NULL){
					$temp = $columnas[($i*2)+1]."=".$datos[$i];
				}else{
					$temp = $columnas[($i*2)+1]."=".$columnas[($i*2)+1]. "+".$datos[$i] ;
				}
				if($cadena == ""){
					$cadena = $temp;
				}else{
					$cadena =$cadena." ".$temp;
				}
			}
		}
		return $cadena;
	}
	
	
function recolecta($datos) {
		$cadena = "";
		$columnas = array_keys($datos);
		for($i = 12;$i <= 60; $i++){
			if($datos[$i] > 0){
					$temp = "nombre='".$columnas[($i*2)+1]."' ";
				if($cadena == ""){
					$cadena = $temp;
				}else{
					$cadena =$cadena." OR ".$temp;
				}
			}
		}
		return $cadena;
	}

function establece_base($datos) {
	$cadena1 = "planetaactual, dueno";
	$cadena2 = "'".$datos["planetadestino"]."', '".$datos["jugador"]."'";
		$columnas = array_keys($datos);
		for($i = 12;$i <= 60; $i++){
			if($datos[$i] > 0){

				$cadena1 =$cadena1.", ".$columnas[($i*2)+1];
				$cadena2 =$cadena2.", '".$datos[$i]."'";
			}
		}
		
		
		return " (".$cadena1.") VALUES (".$cadena2.")";
	
}



//bucle que ejecuta continuamente el motor
while(true){
echo 'inicio bucle/ ';
//comprueba si se ha perdido la conexión con la bd, en cuyo caso la reestablece
if( !mysql_ping($conecta) ){
	mysql_close($conecta);
	$conecta = mysql_connect('localhost','sgcon','gool34');
	$seleciona_bd = mysql_select_db('sgcon');
	 if( !mysql_ping($conecta) || !$seleciona_bd ){
	 echo 'conexión perdida';
	 sleep(5);
	 continue;
	 }
	
}

//Hacemos dos consultas sql. Para ver cual de los dos campos(horavuelta o horallegada) tiene un valor menor(mas proximo en el tiempo) y usar ese ya que es el que interesa. 
//excluimos los movimientos de despliegue ya que cuando estos llegan a su destino el motor no debe actuar.
$query_mov_pend_temp1 = mysql_query('SELECT * FROM mov_pend WHERE horavuelta IS NULL AND tipo > \'1\' ORDER BY horallegada ASC LIMIT 1')or die(mysql_error());
$mov_pend_temp1 = mysql_fetch_array($query_mov_pend_temp1);
// se usa -horavuelta DESC y no hora vuelta ASC, porque en el segundo caso devuelve primero los valores null, para evitar esto se usa la inversa(-) de DESC 
//que devuelve los null al final pero ordenados de forma ascendente
$query_mov_pend_temp2 = mysql_query('SELECT * FROM mov_pend ORDER BY -horavuelta DESC LIMIT 1')or die(mysql_error());
$mov_pend_temp2 = mysql_fetch_array($query_mov_pend_temp2);


//primero comprobamos si hay alguna misión del tipo "vuelta"
if($mov_pend_temp2["horavuelta"] != NULL){
	echo "if 1";
	//comprobamos si hay alguna misión del tipo "ida"
	if($mov_pend_temp1["horallegada"] != NULL){
		echo "if5";
		if($mov_pend_temp1["horallegada"] >= $mov_pend_temp2["horavuelta"]){
			echo "if2";
			$mov_pend = $mov_pend_temp2;
			$mov_pend_tipo = 1;
		}elseif($mov_pend_temp1["horallegada"] < $mov_pend_temp2["horavuelta"] && $mov_pend_temp1["tipo"] == 1 ){
			$mov_pend = $mov_pend_temp2;
			$mov_pend_tipo = 1;
		}else{
			echo "if3";
			$mov_pend = $mov_pend_temp1;
			$mov_pend_tipo = 2;
		}
	}else{
		echo "if6";
		$mov_pend = $mov_pend_temp2;
		$mov_pend_tipo = 1;
	}
}else{
	
	echo "if4";
	$mov_pend = $mov_pend_temp1;
	$mov_pend_tipo = 2;
}

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
	//if que comprueba si dependiendo del tipo de mision y de si va o regresa el motor debe actuar o no
	if($mov_pend_tipo == 2){
		echo "mov1";
		if ($tiempo_mov > $mov_pend["horallegada"]){
			echo "mov2";
			if($mov_pend["tipo"] == 2){//explorar tropas
						echo "mov3";
				if($mov_pend["cancelado"] == 0){
								echo "mov4";
					$tiempo_mov = $mov_pend["horallegada"];
					
				}
			}elseif($mov_pend["tipo"] == 3){//recolectar tropas(llegada)
				echo "mov9";
				if($mov_pend["cancelado"] == 0){
					echo "mov10";
					$tiempo_mov = $mov_pend["horavuelta"];
				}
			}elseif($mov_pend["tipo"] == 4){//establecer base tropas(llegada)
				echo "mov12";
				if($mov_pend["cancelado"] == 0){
					echo "mov13";
					$tiempo_mov = $mov_pend["horavuelta"];
				}
			}
		}
	}else{
					echo "mov5";
		if ($tiempo_mov > $mov_pend["horavuelta"]){
						echo "mov6";
			if($mov_pend["tipo"] == 1){//desplegar tropas(regreso)
				if($mov_pend["cancelado"] == 1){
								echo "mov7";
					$tiempo_mov = $mov_pend["horavuelta"];
					
				}
			}elseif($mov_pend["tipo"] == 2){//explorar tropas(regreso)
						echo "mov8";
				$tiempo_mov = $mov_pend["horavuelta"];
			}elseif($mov_pend["tipo"] == 3){//recolectar tropas(regreso)
						echo "mov11";
				$tiempo_mov = $mov_pend["horavuelta"];
			}elseif($mov_pend["tipo"] == 4){//establecer base tropas(regreso)
						echo "mov14";
				$tiempo_mov = $mov_pend["horavuelta"];
			}
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
	echo 'entro en sleep ';
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
	echo "tarea reconocida, entro en sleep el tiempo necesario ";
	echo "tiempo_mov = ". $tiempo_mov." tiempo_actual = ".$tiempo_actual."/".$ejecuta;
	sleep(5);
	continue;
}else if(($tiempo_actual + 5) > $tiempo_tarea){
	$tiemposleep = $tiempo_tarea - $tiempo_actual;
	if($tiemposleep != 0 && $tiemposleep > 0){
		echo "|tiemposleep = ".$tiemposleep."|";
		sleep($tiemposleep);
	}
}


//FASE 3: ejecutar la tarea dependiendo del tipo
if($ejecuta == "movimiento"){
	//despliegue de tropas(regreso)
	echo "ejecuto movimiento";
	if($mov_pend["tipo"] == 1){//desplegar tropas(regreso)
		
		$query_existencias_tropas = mysql_query('SELECT * FROM existencias_tropas WHERE dueno=\''.$mov_pend["jugador"].'\' AND planetaactual=\''.$mov_pend["planetaactual"].'\'') or die(mysql_error());
		$existencias_tropas = mysql_fetch_array($query_existencias_tropas);
		mysql_query('UPDATE existencias_tropas SET '.set_actualizar_existencias($mov_pend, $existencias_tropas).' WHERE dueno=\''.$mov_pend["jugador"].'\' AND planetaactual=\''.$mov_pend["planetaactual"].'\'') or die(mysql_error());
		mysql_query('DELETE FROM mov_pend WHERE id = \''.$mov_pend["id"].'\'') or die(mysql_error());
		continue;
		
		
		//exploración con tropas(llegada)
	}elseif($mov_pend["tipo"] == 2 && $mov_pend["horavuelta"] == NULL){//explorar con tropas (llegada)
		//consulto el nombre del planeta y su dueño
		$query_datos_exploracion1 = mysql_query('SELECT nombre, dueno FROM mapa WHERE id=\''.$mov_pend["planetadestino"].'\'') or die(mysql_error());
		$datos_exploracion1 = mysql_fetch_row($query_datos_exploracion1) or die(mysql_error());
		//consulto las existencias de tropas del planeta
		$query_datos_exploracion2 = mysql_query('SELECT * FROM existencias_tropas WHERE planetaactual=\''.$mov_pend["planetadestino"].'\'') or die(mysql_error());
		if(mysql_num_rows($query_datos_exploracion2) >0){
		$datos_exploracion2 = mysql_fetch_array($query_datos_exploracion2) or die(mysql_error());
		}
		//consulto las existencias de naves del planeta
		$query_datos_exploracion3 = mysql_query('SELECT * FROM existencias_naves WHERE planetaactual=\''.$mov_pend["planetadestino"].'\'') or die(mysql_error());
		if(mysql_num_rows($query_datos_exploracion3) >0){
			$datos_exploracion3 = mysql_fetch_array($query_datos_exploracion3) or die(mysql_error());
		}
		//consulto las existencias de defensas del planeta
		$query_datos_exploracion4 = mysql_query('SELECT * FROM existencias_defensas WHERE planetaactual=\''.$mov_pend["planetadestino"].'\'') or die(mysql_error());
		if(mysql_num_rows($query_datos_exploracion4) >0){
		 $datos_exploracion4 = mysql_fetch_array($query_datos_exploracion4) or die(mysql_error());
		}
		//consulto los movimientos pendientes para ver si ha llegado algún despliegue(de momento solo despliegue de tropas, falta despliegues de naves)
		$query_datos_exploracion5 = mysql_query('SELECT * FROM mov_pend WHERE planetadestino=\''.$mov_pend["planetadestino"].'\' AND cancelado=\'0\' AND horallegada<=\''.time().'\' AND tipo=\'1\'') or die(mysql_error());
		if(mysql_num_rows($query_datos_exploracion5) >0){
			$datos_exploracion5 = mysql_fetch_array($query_datos_exploracion5) or die(mysql_error());
		}
		//proceso los datos
		$mensaje= "Nombre del Planeta:".$datos_exploracion1[0]."	Dueño:".$datos_exploracion1[1]."<br>";
		//proceso las tropasls
		if($datos_exploracion2[0] != NULL){
			$mensaje = $mensaje."TROPAS:<br>";
			$columnas = array_keys($datos_exploracion2);
			// ATENCION: cambiar este for si se cambia el numero de entradas de la base de datos(esto hay que mejorarlo)
			for($i = 3; $i <= 20 ;$i++){
				if($datos_exploracion2[$i] != NULL){
					$mensaje = $mensaje.$columnas[($i*2)+1]."=".$datos_exploracion2[$i]."<br>";
					
				}
			}
			
		}
		//proceso las naves
		if(isset($datos_exploracion3)){
			$mensaje = $mensaje."NAVES:<br>";
			$columnas = array_keys($datos_exploracion3);
			// ATENCION: cambiar este for si se cambia el numero de entradas de la base de datos(esto hay que mejorarlo)
			for($i = 3; $i <= 24 ;$i++){
				if($datos_exploracion3[$i] != NULL){
					$mensaje = $mensaje.$columnas[($i*2)+1]."=".$datos_exploracion3[$i]."<br>";
				}
			}
		}
		//proceso las defensas
		if(isset($datos_exploracion4)){
			$mensaje = $mensaje."DEFENSAS:<br>";
			$columnas = array_keys($datos_exploracion4);
			// ATENCION: cambiar este for si se cambia el numero de entradas de la base de datos(esto hay que mejorarlo)
			for($i = 3; $i <= 14 ;$i++){
				if($datos_exploracion4[$i] != NULL){
					$mensaje = $mensaje.$columnas[($i*2)+1]."=".$datos_exploracion4[$i]."<br>";
				}
			}
		}
		//proceso los despliegues
			//proceso las defensas
		if(isset($datos_exploracion5)){
			$mensaje = $mensaje."DESPLIEGUES:<br>";
			$columnas = array_keys($datos_exploracion5);
			// ATENCION: cambiar este for si se cambia el numero de entradas de la base de datos(esto hay que mejorarlo)
			for($i = 12; $i <= 60 ;$i++){
				if($datos_exploracion5[$i] != NULL){
					$mensaje = $mensaje.$columnas[($i*2)+1]."=".$datos_exploracion5[$i]."<br>";
				}
			}
		}
		//envio el mensaje
		mysql_query('INSERT INTO mensajes (de, para, hora, asunto, mensaje) VALUES (\'Sistema\',\''.$mov_pend["jugador"].'\', \''.time().'\', \'Exploración de:'.$datos_exploracion1[0].'\', \''.$mensaje.'\')') or die(mysql_error());
		
		//pongo a las tropas exploradoras de vuelta(60 segundos fijos porque todas las tropas tardan eso)
		echo time();
		echo 'UPDATE mov_pend SET horavuelta=\''.(time() + (60 / $config["velocidad_tropas"] / 10)).'\' WHERE id=\''.$mov_pend["id"].'\'';
		mysql_query('UPDATE mov_pend SET horavuelta=\''.(time() + 60).'\' WHERE id=\''.$mov_pend["id"].'\'') or die(mysql_error());
		continue;
		
		
	}elseif($mov_pend["tipo"] == 2 && $mov_pend["horavuelta"] != NULL){//exploracion con tropas(regreso)
		$query_existencias_tropas = mysql_query('SELECT * FROM existencias_tropas WHERE dueno=\''.$mov_pend["jugador"].'\' AND planetaactual=\''.$mov_pend["planetaactual"].'\'') or die(mysql_error());
		$existencias_tropas = mysql_fetch_array($query_existencias_tropas);
		mysql_query('UPDATE existencias_tropas SET '.set_actualizar_existencias($mov_pend, $existencias_tropas).' WHERE dueno=\''.$mov_pend["jugador"].'\' AND planetaactual=\''.$mov_pend["planetaactual"].'\'') or die(mysql_error());
		mysql_query('DELETE FROM mov_pend WHERE id = \''.$mov_pend["id"].'\'') or die(mysql_error());
		continue;
	}elseif($mov_pend["tipo"] == 3 && $mov_pend["horavuelta"] == NULL){//recolección con tropas(llegada)
		//falta el código de la recolección
		echo 'SELECT carga FROM unidades WHERE '.recolecta($mov_pend).'';
		$query_carga = mysql_query('SELECT carga FROM unidades WHERE '.recolecta($mov_pend).'')or die(mysql_error());
		$query_porcentaje = mysql_query('SELECT porcentaje FROM mapa WHERE id=\''.$mov_pend["planetadestino"].'\'')or die(mysql_error());
		$porcentaje = mysql_fetch_row($query_porcentaje);
		$indice=0;
		$carga_array = array();
		while($carga = mysql_fetch_row($query_carga)){
			$carga_array[$indice] = $carga[0];
			$indice++;
		}
		$indice = 0;
		$carga_subtotal = 0;
		for($i = 12; $i <= 60; $i++){
			if($mov_pend[$i] > 0){
				$carga_subtotal = $carga_subtotal + ($mov_pend[$i] * $carga_array[$indice]);
			}
		}
		
		$carga_total = $carga_subtotal * $porcentaje[0] / 100;
		
		$recurso1 = $carga_total /10 ;
		$recurso2 = $carga_total - $recurso1;
		
		mysql_query('UPDATE mov_pend SET horavuelta=\''.(time() + (60 / $config["velocidad_tropas"] / 10)).'\', recurso1=\''.$recurso1.'\' , recurso2=\''.$recurso2.'\' WHERE id=\''.$mov_pend["id"].'\'') or die(mysql_error());
		$mensaje = "<p>recurso1=".$recurso1."</p><p>recurso2=".$recurso2."</p>" ;
		//envio el mensaje
		mysql_query('INSERT INTO mensajes (de, para, hora, asunto, mensaje) VALUES (\'Sistema\',\''.$mov_pend["jugador"].'\', \''.time().'\', \'Informe de Recolección \', \''.$mensaje.'\')') or die(mysql_error());
		continue;
	}elseif($mov_pend["tipo"] == 3 && $mov_pend["horavuelta"] != NULL){//recolección con tropas(regreso)
	
		$query_existencias_tropas = mysql_query('SELECT * FROM existencias_tropas WHERE dueno=\''.$mov_pend["jugador"].'\' AND planetaactual=\''.$mov_pend["planetaactual"].'\'') or die(mysql_error());
		$existencias_tropas = mysql_fetch_array($query_existencias_tropas);
		mysql_query('UPDATE existencias_tropas SET '.set_actualizar_existencias($mov_pend, $existencias_tropas).' WHERE dueno=\''.$mov_pend["jugador"].'\' AND planetaactual=\''.$mov_pend["planetaactual"].'\'') or die(mysql_error());
		mysql_query('UPDATE jugadores SET recurso1=recurso1+'.$mov_pend["recurso1"].', recurso2=recurso2+'.$mov_pend["recurso2"].' WHERE nick=\''.$mov_pend["jugador"].'\' ') or die(mysql_error());
		mysql_query('DELETE FROM mov_pend WHERE id = \''.$mov_pend["id"].'\'') or die(mysql_error());
		continue;

	}elseif($mov_pend["tipo"] == 4 && $mov_pend["horavuelta"] == NULL){//Establecer base con tropas
		
		$query_info_planeta = mysql_query('SELECT dueno FROM mapa WHERE id=\''.$mov_pend["planetadestino"].'\'') or die(mysql_error());
		$info_planeta = mysql_fetch_row($query_info_planeta) or die(mysql_error());
		if($info_planeta[0] == NULL){
			mysql_query('UPDATE mapa SET dueno=\''.$mov_pend["jugador"].'\' WHERE id=\''.$mov_pend["planetadestino"].'\'') or die(mysql_error());
			$query_planeta_vacio = mysql_query('SELECT raza, planeta1, planeta2, planeta3, planeta4, planeta5 FROM jugadores WHERE nick=\''.$mov_pend["jugador"].'\'') or die(mysql_error());
			$planeta_vacio= mysql_fetch_row($query_planeta_vacio);
			$query_lim_planetas = mysql_query('SELECT limiteplanetas FROM raza WHERE nombre=\''.$planeta_vacio[0].'\'') or die(mysql_error());
			$lim_planetas= mysql_fetch_row($query_lim_planetas) or die(mysql_error());
			$exito = false;
			for($i=1;$i<= $lim_planetas[0];$i++){
				if($planeta_vacio[$i] == NULL){
					mysql_query('UPDATE jugadores SET planeta'.$i.'=\''.$mov_pend["planetadestino"].'\' WHERE nick=\''.$mov_pend["jugador"].'\'') or die(mysql_error());
					$exito= true;
					break;
				}
			}
			if($exito== true){
				mysql_query('INSERT INTO existencias_tropas'.establece_base($mov_pend)) or die(mysql_error());
				mysql_query('DELETE FROM mov_pend WHERE id=\''.$mov_pend["id"].'\'') or die(mysql_error());
			}else{
				mysql_query('UPDATE mov_pend SET cancelado=\'1\', horavuelta=\''.(time() + (43200 / $config["velocidad_tropas"] / 10)).'\' WHERE id=\''.$mov_pend["id"].'\'')or die(mysql_error());
			}
		}
		continue;

	}elseif($mov_pend["tipo"] == 4 && $mov_pend["horavuelta"] != NULL){//Establecer base con tropas(regreso)
	
		$query_existencias_tropas = mysql_query('SELECT * FROM existencias_tropas WHERE dueno=\''.$mov_pend["jugador"].'\' AND planetaactual=\''.$mov_pend["planetaactual"].'\'') or die(mysql_error());
		$existencias_tropas = mysql_fetch_array($query_existencias_tropas);
		mysql_query('UPDATE existencias_tropas SET '.set_actualizar_existencias($mov_pend, $existencias_tropas).' WHERE dueno=\''.$mov_pend["jugador"].'\' AND planetaactual=\''.$mov_pend["planetaactual"].'\'') or die(mysql_error());
		mysql_query('DELETE FROM mov_pend WHERE id = \''.$mov_pend["id"].'\'') or die(mysql_error());
		continue;

	}
					
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
	echo "investigacion";
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
?>

