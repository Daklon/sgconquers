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
    <td width="144">Asunto</td>
    <td width="139">Enviado Por</td>
    <td width="144">Hora</td>
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
<td><a href="juego-index.php?control=leer_mensaje&id=<?php echo $mensaje["id"] ?>"><?php echo date("H:i:s j-n-Y",$mensaje["hora"]);?></a></td>
<td width="53"><a href="juego-index.php?control=mensajes&accion=borrar_mensaje&id=<?php echo $mensaje["id"] ?>">Eliminar</a></td>

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
    <?php
	if($_GET["accion"] == "responder"){
		$responder_destinatario = htmlentities($_GET["destinatario"]);
	?>
    <p>Para: <input type="text" name="destinatario" value="<?php echo $responder_destinatario; ?>"/></p>
	<?php
	}else{
	?>
    <p>Para: <input type="text" name="destinatario"/></p>
    <?php
	}
	?>
    <p>Asunto: <input type="text" name="asunto"/></p>
    <p><textarea name="cuerpo"></textarea></p>
    <input type="hidden" name="hora" value="<?php echo time();?>"/>
    <input type="hidden" name="remitente" value="<?php echo $_SESSION["nick"]; ?>"/>
    <p><input type="submit" name="enviar_mensaje" value="Enviar" /></p>
    </form>
    </div>
	</div>
    <?php
	if($_GET["accion"] == "responder"){
	echo '<script>cambio(2)</script>';	
	}
	?>