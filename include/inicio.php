<div id="principal">
<p>Juego en fase pre-Alpha, aún no está ni de lejos listo para ser jugado, pero quiero ir haciendo pruebas para probar lo que tengo hecho, si todo va bien no debería haber ningún error. Los apartados que pueden probar son: Investigaciones, Tropas, Naves, Defensas y Mensajes, el resto aún no está listo</p>
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
