<main>

    <?php
			if (isset($_SESSION["login"]) && ($_SESSION["login"]===true)){
			
				require_once('comun/sideBarIzq.php');
				require_once('comun/contentMain.php');
			}
			else{
				echo '<div id="panelVacio">
						<p> Informacion de qué va el proyecto y explicar que esta hecho para una asignatura</p>
					</div>';
			}
	?>


</main>