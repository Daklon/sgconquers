<?php
//esto hay que optimizarlo(sobretodo reducir el numero de consultas a la bd) y acabarlo
	if (isset($_POST["galaxia"])) {
	//ejecuta esto si hemos selecionado una zona diferente a la de nuestro planeta
		$query_planetas = mysql_query('SELECT id, nombre, dueno, porcentaje FROM mapa WHERE galaxia =\''.$_POST["galaxia"].'\' AND sector =\''.$_POST["sector"].'\' AND cuadrante =\''.$_POST["cuadrante"].'\' ORDER BY id ASC') or die(mysql_error());
		$query_lista_galaxias = mysql_query('SELECT galaxia FROM mapa_estructura') or die(mysql_error());
		$query_datos_galaxia = mysql_query('SELECT sectores, cuadrantes, posiciones FROM mapa_estructura WHERE galaxia =\''.$_POST["galaxia"].'\'') or die(mysql_error());
		$datos_galaxia = mysql_fetch_array($query_datos_galaxia);
		
		//esto es necesario para que mas abajo se ejecuten los if que selecionan las coordenadas actuales por defecto en los selectbox
		$galaxia_actual["galaxia"] = $_POST["galaxia"];
		$galaxia_actual["sector"] = $_POST["sector"];
		$galaxia_actual["cuadrante"] = $_POST["cuadrante"];
	}else{
	//ejecuta esto nada mas entrar a la vista de galaxia para mostrar los alrededores de nuestro planeta
		$query_galaxia_actual = mysql_query('SELECT galaxia, sector, cuadrante FROM mapa WHERE id = \''.$_SESSION["planeta"].'\'') or die(mysql_error());
		$galaxia_actual = mysql_fetch_array($query_galaxia_actual);
		$query_planetas = mysql_query('SELECT id, nombre, dueno, porcentaje FROM mapa WHERE galaxia =\''.$galaxia_actual["galaxia"].'\' AND sector =\''.$galaxia_actual["sector"].'\' AND cuadrante =\''.$galaxia_actual["cuadrante"].'\' ORDER BY id ASC') or die(mysql_error());
		$query_lista_galaxias = mysql_query('SELECT galaxia FROM mapa_estructura') or die(mysql_error());
		$query_datos_galaxia = mysql_query('SELECT sectores, cuadrantes, posiciones FROM mapa_estructura WHERE galaxia =\''.$galaxia_actual["galaxia"].'\'') or die(mysql_error());
		$datos_galaxia = mysql_fetch_array($query_datos_galaxia);
	}
	//esto es para generar el formulario donde se seleciona la zona que se quiere ver(falta modificar que se muestre la vista actual en las casillas)
?>
	<div id="principal">
	
    <form action="juego-index.php?control=galaxia" method="post" name="selec_galaxia">
    <table>
    <tr>
    <td>Galaxia:<select name="galaxia" onchange="document.selec_galaxia.submit();">
    <?php 
	while($lista_galaxias = mysql_fetch_array($query_lista_galaxias)){
		if($galaxia_actual["galaxia"] == $lista_galaxias["galaxia"]){
			echo '<option selected>'.$lista_galaxias["galaxia"].'</option>';
		}else{
			echo '<option>'.$lista_galaxias["galaxia"].'</option>';
		}
	}
	?>
    </select></td>
    <td>Sector:<select name="sector" onchange="document.selec_galaxia.submit();">
    <?php 
	for($i= 1; $i <= $datos_galaxia["sectores"];$i++){
		if($galaxia_actual["sector"] == $i){
			echo '<option selected>'.$i.'</option>';
		}else{
			echo '<option>'.$i.'</option>';
		}
	}
	?>
    </select></td>
    <td>Cuadrante:<select name="cuadrante" onchange="document.selec_galaxia.submit();">
    <?php 
	for($i= 1; $i <= $datos_galaxia["cuadrantes"];$i++){
		if($galaxia_actual["cuadrante"] == $i){
			echo '<option selected>'.$i.'</option>';
		}else{
			echo '<option>'.$i.'</option>';
		}
	}
	?>
    </select></td>
    </tr>
    </table>
    </form>
	
	<table>
	<?php
	//crea una tabla con todos los planetas del cuadrante
	while($datos_planetas = mysql_fetch_array($query_planetas)){
		//modifica el valor de NULL por "sin dueño" para que quede mejor
		if($datos_planetas[2] == NULL){
			$datos_planetas[2] = "Sin Dueño";
		}
		echo "<tr>";
		echo "<td>".$datos_planetas[0]."</td><td>".$datos_planetas[1]."</td><td>".$datos_planetas[2]."</td><td>".$datos_planetas[3]."%</td>";
		echo "</tr>";
	}
	?>
	</table>
</div>