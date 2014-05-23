<div id="principal">
<p>Juego en fase Alpha, ya se pueden ir probando cosillas, en caso de que salga algún error(que van a salir, porque seguro que hay muchos), copienlo y pasenmelo. Los apartados que pueden probar son: Investigaciones, Tropas, Naves, Defensas, Galaxia, Mensajes, el resto aún no está listo. Se pueden hacer misiones sencillas como desplegar. A TESTEAR!!!!!</p>
</div>
<?php
//genera caja mision si es necesario(una caja por mision)


//funcion que genera una cadena para sql que consulta todos los ids de los planetas del jugador(para poder ver todas las misiones de los planetas del jugador)
function id_planetas($planeta1, $planeta2, $planeta3, $planeta4, $planeta5){
	$cadena = "";
	$planeta = array();
	$planeta[0] = $planeta1;
	$planeta[1] = $planeta2;
	$planeta[2] = $planeta3;
	$planeta[3] = $planeta4;
	$planeta[4] = $planeta5;
	for($i=0; $i < 5; $i++){
		if($planeta[$i] != NULL){
			if($cadena == ""){
				$cadena = "planetadestino=".$planeta[$i]."";
			}else{
				$cadena = $cadena. " OR planetadestino=".$planeta[$i];
			}			
		}
	}
	return $cadena;
}

//obtenemos una lista con los ids de los movimientos para procesarlos con otras cosnultas en base al id mas adelante
$query_mov_pend = mysql_query('SELECT id FROM mov_pend WHERE jugador=\''.$_SESSION['nick'].'\' OR '.id_planetas($jugador_dat['planeta1'],$jugador_dat['planeta2'], $jugador_dat['planeta3'], $jugador_dat['planeta4'], $jugador_dat['planeta5']).'')or die(mysql_error());

	
	//generamos una caja con la consulta sql para cada uno de los ids obtenidos antes y sus datos asociados
	$indice = 1;
	while($mov_pend = mysql_fetch_row($query_mov_pend)){
		$query_datos_mov = mysql_query('SELECT * FROM mov_pend WHERE id=\''.$mov_pend[0].'\' ') or die(mysql_error());
		$datos_mov = mysql_fetch_array($query_datos_mov);
		
		//truco para obtener los nombres de las columnas
		$columnas = array_keys($datos_mov);
		//consultas sql para obtener nombre y coordenadas de los planetas
		$query_coord_planetaactual = mysql_query('SELECT nombre FROM mapa WHERE id=\''.$datos_mov["planetaactual"].'\' ') or die(mysql_error());
		$query_coord_planetadestino = mysql_query('SELECT nombre FROM mapa WHERE id=\''.$datos_mov["planetadestino"].'\' ') or die(mysql_error());
		//empezamos a procesar los datos
		$planetas_mov = array();
		$temp = mysql_fetch_row($query_coord_planetaactual);
		$planetas_mov[1] = $temp[0];
		$temp = mysql_fetch_row($query_coord_planetadestino);
		$planetas_mov[2] = $temp[0];
			
		if($datos_mov["horavuelta"] == NULL){
			$tiempo_restante = $datos_mov["horallegada"] - time();
		}else{
			$tiempo_restante = $datos_mov["horavuelta"] - time();
		}
		if($datos_mov["tipo"] == 1){
			$tipo = "Desplegar";
		}elseif($datos_mov["tipo"] == 2){
			$tipo = "Explorar";
		}elseif($datos_mov["tipo"] == 3){
			$tipo = "Recolectar";
		}elseif($datos_mov["tipo"] == 4){
			$tipo = "Establecer Base";
		}
		//este script simplemente genera contadores(para mostrar el tiempo restante ) para cada movimiento
		?>
    <script type="text/javascript" language="JavaScript"> 
var tiempo_mov<?php echo $indice ?> = <?php echo $tiempo_restante; ?>;
function contador_mov<?php echo $indice ?>(){
	if(tiempo_mov<?php echo $indice ?> >= 86400){
		var dias_mov<?php echo $indice ?> =  Math.floor(tiempo_mov<?php echo $indice ?> / 86400);
		var horas_mov<?php echo $indice ?> = Math.floor(tiempo_mov<?php echo $indice ?> / 3600- (dias_mov<?php echo $indice ?> *24));
		var minutos_mov<?php echo $indice ?> = tiempo_mov<?php echo $indice ?>/60 - (dias_mov<?php echo $indice ?> * 24 * 60);
		var minutos_mov<?php echo $indice ?> = Math.floor(minutos_mov<?php echo $indice ?>);
		var segundos_mov<?php echo $indice ?> = tiempo_mov<?php echo $indice ?> % 60;
		if(dias_mov<?php echo $indice ?> > 1){
			document.formulario_mov<?php echo $indice ?>.reloj.value=dias + " dias "+ horas_mov<?php echo $indice ?> + ":" +minutos_mov<?php echo $indice ?> +":"+ segundos_mov<?php echo $indice ?>;
		}else{
			document.formulario_mov<?php echo $indice ?>.reloj.value=dias + " dia "+ horas_mov<?php echo $indice ?> + ":" +minutos_mov<?php echo $indice ?> +":"+ segundos_mov<?php echo $indice ?>;
		}
		
	}else if (tiempo_mov<?php echo $indice ?> >= 3600){
		var horas_mov<?php echo $indice ?> = Math.floor(tiempo_mov<?php echo $indice ?> / 3600);
		var minutos_mov<?php echo $indice ?> = tiempo_mov<?php echo $indice ?>/60 - (horas_mov<?php echo $indice ?> * 60);
		var minutos_mov<?php echo $indice ?> = Math.floor(minutos_mov<?php echo $indice ?>);
		var segundos_mov<?php echo $indice ?> = tiempo_mov<?php echo $indice ?> % 60;
		document.formulario_mov<?php echo $indice ?>.reloj.value= horas_mov<?php echo $indice ?> + ":" +minutos_mov<?php echo $indice ?> +":"+ segundos_mov<?php echo $indice ?>;
	}else if (tiempo_mov<?php echo $indice ?> >= 60){
		var minutos_mov<?php echo $indice ?> = tiempo_mov<?php echo $indice ?>/60;
		var minutos_mov<?php echo $indice ?> = Math.floor(minutos_mov<?php echo $indice ?>);
		var segundos_mov<?php echo $indice ?> = tiempo_mov<?php echo $indice ?> % 60;
		document.formulario_mov<?php echo $indice ?>.reloj.value= minutos_mov<?php echo $indice ?> +":"+ segundos_mov<?php echo $indice ?>;
	}else if(tiempo_mov<?php echo $indice ?> < 60 && tiempo_mov<?php echo $indice ?> >= 0){
		var segundos_mov<?php echo $indice ?> = tiempo_mov<?php echo $indice ?>;
		document.formulario_mov<?php echo $indice ?>.reloj.value= segundos_mov<?php echo $indice ?>;
	}
	tiempo_mov<?php echo $indice ?>--;
	if (tiempo_mov<?php echo $indice ?> == -1){
		location.reload();
	}
}
window.onload = contador_mov<?php echo $indice ?>;

setInterval("contador_mov<?php echo $indice ?>()",1000);  
</script> 
    <?php
		echo '<div id="principal">';
		echo '<table><tr>';
		echo '<td>'.$tipo.'</td>';
		//imprime los nombres de los planetas en base a la consulta a la bd
		echo '<td>'.$planetas_mov[1].' --> '.$planetas_mov[2].'</td>';
		echo '<td><form name="formulario_mov'.$indice.'"><input type="text" name="reloj" value="" size="55" style="background-color:#006; color:#FFF; border : 0px ; text-align : center"></form> </td>';
		if($datos_mov["cancelado"] == 0){
			echo '<td><form action="juego-index.php?control=inicio" name="regresar_mov'.$indice.'" method="post"><input type="submit" name="cancelar_mov" value="Regresar"> <input type="hidden" name="id_mov" value="'.$mov_pend[0].'"> </form> </td>';
		}
		echo '</tr>';
		$indice_tropas = 12;
		while($columnas[($indice_tropas*2)+1] != NULL){
			if($datos_mov[$indice_tropas] > 0){
				echo "<tr><td>".$columnas[($indice_tropas*2)+1].": ".$datos_mov[$indice_tropas]."</td></tr>";
			}
			
			$indice_tropas++;
		}
		echo '</table></div>';
		$indice++;
	}







//genera caja investigación si es necesario
$query_inves_pend = mysql_query('SELECT investigacion, nivelnuevo, horafinalizar FROM inves_pend WHERE jugador=\''.$_SESSION['nick'].'\' AND cancelado=\'0\'')or die(mysql_error());
$inves_pend = mysql_fetch_array($query_inves_pend);
	$query_nombre_inves = mysql_query('SELECT nombre FROM investigaciones WHERE raza=\''.$_SESSION['raza'].'\' AND numero=\''.$inves_pend["investigacion"].'\'')or die(mysql_error());
	$nombre_inves = mysql_fetch_array($query_nombre_inves);
	
	
	if(!empty($inves_pend)){
		$tiempo_restante = $inves_pend["horafinalizar"] - time();
		?>
    <script type="text/javascript" language="JavaScript"> 
var tiempo_inves = <?php echo $tiempo_restante; ?>;
function contador_inves(){
	if(tiempo_inves >= 86400){
		var dias_inves =  Math.floor(tiempo_inves / 86400);
		var horas_inves = Math.floor(tiempo_inves / 3600- (dias_inves *24));
		var minutos_inves = tiempo_inves/60 - (dias_inves * 24 * 60);
		var minutos_inves = Math.floor(minutos_inves);
		var segundos_inves = tiempo_inves % 60;
		if(dias_inves > 1){
			document.formulario_inves.reloj.value=dias + " dias "+ horas_inves + ":" +minutos_inves +":"+ segundos_inves;
		}else{
			document.formulario_inves.reloj.value=dias + " dia "+ horas_inves + ":" +minutos_inves +":"+ segundos_inves;
		}
		
	}else if (tiempo_inves >= 3600){
		var horas_inves = Math.floor(tiempo_inves / 3600);
		var minutos_inves = tiempo_inves/60 - (horas_inves * 60);
		var minutos_inves = Math.floor(minutos_inves);
		var segundos_inves = tiempo_inves % 60;
		document.formulario_inves.reloj.value= horas_inves + ":" +minutos_inves +":"+ segundos_inves;
	}else if (tiempo_inves >= 60){
		var minutos_inves = tiempo_inves/60;
		var minutos_inves = Math.floor(minutos_inves);
		var segundos_inves = tiempo_inves % 60;
		document.formulario_inves.reloj.value= minutos_inves +":"+ segundos_inves;
	}else if(tiempo_inves < 60 && tiempo_inves >= 0){
		var segundos_inves = tiempo_inves;
		document.formulario_inves.reloj.value= segundos_inves;
	}
	tiempo_inves--;
	if (tiempo_inves == -1){
		location.reload();
	}
}
window.onload = contador_inves;

setInterval("contador_inves()",1000);  
</script> 
    <?php
		echo '<div id="principal">';
		echo '<table><tr>';
		echo '<td>Investigación: '.$nombre_inves["nombre"].'('.$inves_pend["nivelnuevo"].')</td>';
		echo '<td><form name="formulario_inves"><input type="text" name="reloj" value="" size="55" style="background-color:#006; color:#FFF; border : 0px ; text-align : center"></form> </td>';
		echo '</tr></table></div>';
	}
	
	//genera caja para construcción tropas si es necesario
	$query_cons_pend = mysql_query('SELECT unidad, cantidad, horafinalizar FROM cons_pend WHERE jugador=\''.$_SESSION['nick'].'\' AND cancelado=\'0\' AND planeta=\''.$_SESSION["planeta"].'\' AND tipo=\'tropas\'')or die(mysql_error());
$cons_pend = mysql_fetch_array($query_cons_pend);
	
	if(!empty($cons_pend)){
		$tiempo_restante = $cons_pend["horafinalizar"] - time();
		?>
    <script type="text/javascript" language="JavaScript"> 
var tiempo_tropas = <?php echo $tiempo_restante; ?>;
function contador_tropas(){
	if(tiempo_tropas >= 86400){
		var dias_tropas =  Math.floor(tiempo_tropas / 86400);
		var horas_tropas = Math.floor(tiempo_tropas / 3600- (dias_tropas *24));
		var minutos_tropas = tiempo_tropas/60 - (dias_tropas * 24 * 60);
		var minutos_tropas = Math.floor(minutos_tropas);
		var segundos_tropas = tiempo_tropas % 60;
		if(dias > 1){
			document.formulario_tropas.reloj.value=dias_tropas + " dias "+ horas_tropas + ":" +minutos_tropas +":"+ segundos_tropas;
		}else{
			document.formulario_tropas.reloj.value=dias_tropas + " dia "+ horas_tropas + ":" +minutos_tropas +":"+ segundos_tropas;
		}
		
	}else if (tiempo_tropas >= 3600){
		var horas_tropas = Math.floor(tiempo_tropas / 3600);
		var minutos_tropas = tiempo_tropas/60 - (horas_tropas * 60);
		var minutos_tropas = Math.floor(minutos_tropas);
		var segundos_tropas = tiempo_tropas % 60;
		document.formulario_tropas.reloj.value= horas_tropas + ":" +minutos_tropas +":"+ segundos_tropas;
	}else if (tiempo_tropas >= 60){
		var minutos_tropas = tiempo_tropas/60;
		var minutos_tropas = Math.floor(minutos_tropas);
		var segundos_tropas = tiempo_tropas % 60;
		document.formulario_tropas.reloj.value= minutos_tropas +":"+ segundos_tropas;
	}else if(tiempo_tropas < 60 && tiempo_tropas >= 0){
		var segundos_tropas = tiempo_tropas;
		document.formulario_tropas.reloj.value= segundos_tropas;
	}
	tiempo_tropas--;
	if (tiempo_tropas == -1){
		location.reload();
	}
}
window.onload = contador_tropas;

setInterval("contador_tropas()",1000);  
</script> 
    <?php
		echo '<div id="principal">';
		echo '<table><tr>';
		echo '<td> '.$cons_pend["unidad"].'('.$cons_pend["cantidad"].')</td>';
		echo '<td><form name="formulario_tropas"><input type="text" name="reloj" value="" size="55" style="background-color:#006; color:#FFF; border : 0px ; text-align : center"></form> </td>';
		echo '</tr></table></div>';
	}
	
	//genera caja para construcción navess si es necesario
	$query_cons_pend = mysql_query('SELECT unidad, cantidad, horafinalizar FROM cons_pend WHERE jugador=\''.$_SESSION['nick'].'\' AND cancelado=\'0\' AND planeta=\''.$_SESSION["planeta"].'\' AND tipo=\'naves\'')or die(mysql_error());
$cons_pend = mysql_fetch_array($query_cons_pend);
	
	if(!empty($cons_pend)){
		$tiempo_restante = $cons_pend["horafinalizar"] - time();
		?>
    <script type="text/javascript" language="JavaScript"> 
var tiempo_naves = <?php echo $tiempo_restante; ?>;
function contador_naves(){
	if(tiempo_naves >= 86400){
		var dias_naves =  Math.floor(tiempo_naves / 86400);
		var horas_naves = Math.floor(tiempo_naves / 3600- (dias_naves *24));
		var minutos_naves = tiempo_naves/60 - (dias_naves * 24 * 60);
		var minutos_naves = Math.floor(minutos_naves);
		var segundos_naves = tiempo_naves % 60;
		if(dias_naves > 1){
			document.formulario_naves.reloj.value=dias_naves + " dias "+ horas_naves + ":" +minutos_naves +":"+ segundos_naves;
		}else{
			document.formulario_naves.reloj.value=dias_naves + " dia "+ horas_naves + ":" +minutos_naves +":"+ segundos_naves;
		}
		
	}else if (tiempo_naves >= 3600){
		var horas_naves = Math.floor(tiempo_naves / 3600);
		var minutos_naves = tiempo_naves/60 - (horas_naves * 60);
		var minutos_naves = Math.floor(minutos_naves);
		var segundos_naves = tiempo_naves % 60;
		document.formulario_naves.reloj.value= horas_naves + ":" +minutos_naves +":"+ segundos_naves;
	}else if (tiempo_naves >= 60){
		var minutos_naves = tiempo_naves/60;
		var minutos_naves = Math.floor(minutos_naves);
		var segundos_naves = tiempo_naves % 60;
		document.formulario_naves.reloj.value= minutos_naves +":"+ segundos_naves;
	}else if(tiempo_naves < 60 && tiempo_naves >= 0){
		var segundos_naves = tiempo_naves;
		document.formulario_naves.reloj.value= segundos_naves;
	}
	tiempo_naves--;
	if (tiempo_naves == -1){
		location.reload();
	}
}
window.onload = contador_naves;

setInterval("contador_naves()",1000);  
</script> 
    <?php
		echo '<div id="principal">';
		echo '<table><tr>';
		echo '<td> '.$cons_pend["unidad"].'('.$cons_pend["cantidad"].')</td>';
		echo '<td><form name="formulario_naves"><input type="text" name="reloj" value="" size="55" style="background-color:#006; color:#FFF; border : 0px ; text-align : center"></form> </td>';
		echo '</tr></table></div>';
	}
	
	//genera caja para construcción defensas si es necesario
	$query_cons_pend = mysql_query('SELECT unidad, cantidad, horafinalizar FROM cons_pend WHERE jugador=\''.$_SESSION['nick'].'\' AND cancelado=\'0\' AND planeta=\''.$_SESSION["planeta"].'\' AND tipo=\'defensas\'')or die(mysql_error());
$cons_pend = mysql_fetch_array($query_cons_pend);
	
	if(!empty($cons_pend)){
		$tiempo_restante = $cons_pend["horafinalizar"] - time();
		?>
    <script type="text/javascript" language="JavaScript"> 
var tiempo_defensas = <?php echo $tiempo_restante; ?>;
function contador_defensas(){
	if(tiempo_defensas >= 86400){
		var dias_defensas =  Math.floor(tiempo_defensas / 86400);
		var horas_defensas = Math.floor(tiempo_defensas / 3600- (dias_defensas*24));
		var minutos_defensas = tiempo_defensas/60 - (dias_defensas * 24 * 60);
		var minutos_defensas = Math.floor(minutos_defensas);
		var segundos_defensas = tiempo_defensas % 60;
		if(dias_defensas > 1){
			document.formulario_defensas.reloj.value=dias_defensas + " dias "+ horas_defensas + ":" +minutos_defensas +":"+ segundos_defensas;
		}else{
			document.formulario_defensas.reloj.value=dias_defensas + " dia "+ horas_defensas + ":" +minutos_defensas +":"+ segundos_defensas;
		}
		
	}else if (tiempo_defensas >= 3600){
		var horas_defensas = Math.floor(tiempo_defensas / 3600);
		var minutos_defensas = tiempo_defensas/60 - (horas_defensas * 60);
		var minutos_defensas = Math.floor(minutos_defensas);
		var segundos_defensas = tiempo_defensas % 60;
		document.formulario_defensas.reloj.value= horas_defensas + ":" +minutos_defensas +":"+ segundos_defensas;
	}else if (tiempo_defensas >= 60){
		var minutos_defensas = tiempo_defensas/60;
		var minutos_defensas = Math.floor(minutos_defensas);
		var segundos_defensas = tiempo_defensas % 60;
		document.formulario_defensas.reloj.value= minutos_defensas +":"+ segundos_defensas;
	}else if(tiempo_defensas < 60 && tiempo_defensas >= 0){
		var segundos_defensas = tiempo_defensas;
		document.formulario_defensas.reloj.value= segundos_defensas;
	}
	tiempo_defensas--;
	if (tiempo_defensas == -1){
		location.reload();
	}
}
window.onload = contador_defensas;

setInterval("contador_defensas()",1000);  
</script> 
    <?php
		echo '<div id="principal">';
		echo '<table><tr>';
		echo '<td> '.$cons_pend["unidad"].'('.$cons_pend["cantidad"].')</td>';
		echo '<td><form name="formulario_defensas"><input type="text" name="reloj" value="" size="55" style="background-color:#006; color:#FFF; border : 0px ; text-align : center"></form> </td>';
		echo '</tr></table></div>';
	} 
?>
