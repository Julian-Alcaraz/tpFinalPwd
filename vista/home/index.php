<?php
include_once("../../config.php");
$pagSeleccionada = "Home";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once($ESTRUCTURA . "/header.php"); ?>
    <?php include_once($ESTRUCTURA . "/cabeceraBD.php"); ?>
</head>
<body style="overflow: hidden;">
    <!--  <div class="container text-center my-4">-->
    <div id="fondo">
        <div id="filtro-opacidad">
            <div class="container d-flex align-items-center justify-content-center vh-100 ">
                <h2>Bienvenidos</h2>
            </div>
        </div>
    </div>
    <?php include_once($ESTRUCTURA . "/pie.php"); ?>
</body>
</html>