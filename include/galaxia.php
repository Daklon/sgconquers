<?php
	$query_galaxia_actual = mysql_query('SELECT galaxia, sector, cuadrante FROM mapa WHERE id = \''.$_SESSION["planeta"].'\'') or die(mysql_error());
	$galaxia_actual = mysql_fetch_array($query_galaxia_actual);
	$query_lista_galaxias = mysql_query('SELECT galaxia FROM mapa_estructura') or die(mysql_error());
	$query_datos_galaxia = mysql_query('SELECT sectores, cuadrantes, posiciones FROM mapa_estructura WHERE galaxia =\''.$galaxia_actual["galaxia"].'\'') or die(mysql_error());
	$datos_galaxia = mysql_fetch_array($query_datos_galaxia);
?>
	<div id="principal">
    <form action="juego-index.php?control=galaxia" method="post" name="selec_galaxia">
    <table>
    <tr>
    <td>Galaxia:<select name="galaxia" onchange="document.selec_galaxia.submit();">
    <?php 
	while($lista_galaxias = mysql_fetch_array($query_lista_galaxias)){
		echo '<option>'.$lista_galaxias["galaxia"].'</option>';
	}
	?>
    </select></td>
    <td>Sector:<select name="sector" onchange="document.selec_galaxia.submit();">
    <?php 
	for($i= 1; $i <= $datos_galaxia["sectores"];$i++){
		echo '<option>'.$i.'</option>';
	}
	?>
    </select></td>
    <td>Cuadrante:<select name="cuadrante" onchange="document.selec_galaxia.submit();">
    <?php 
	for($i= 1; $i <= $datos_galaxia["cuadrantes"];$i++){
		echo '<option>'.$i.'</option>';
	}
	?>
    </select></td>
    </tr>
    </table>
    </form>
<p>galaxia.</p>
</div>