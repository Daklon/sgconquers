<?php
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