<?php
//Cargamos las clases necesarias primero

require_once("../backend/producto.php");


?>

<div id="content">
<?php

	if( isset($_SESSION["queryProductos"])){

		$result = $_SESSION["queryProductos"];

		foreach ($result as $key => $value) {
			echo 	"<div class='panelProducto'>
						<p> Hola </p>
					</div>";	
		}

		//<img id='producto' src='.$value.'>*/

		unset($_SESSION["queryProductos"]);
	}
	else{
		echo "NO";
		//Aqui deberian salir productos random

		//header('Location: procesarRandom.php');
	}


?>
</div>';