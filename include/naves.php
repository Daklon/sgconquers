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
<table width="402">
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
					}else if(tiempo < 60 && tiempo >= 0){
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
		convierteseg(($nuevas_naves_dat["tiempo"]/ $config["velocidad_construccion"] / 10));
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