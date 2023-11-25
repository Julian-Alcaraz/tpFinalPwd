<?php
include_once("../../config.php");
$objSession = new Session();
$sesionValida = $objSession->validar();
$menues = [];
if ($sesionValida) {
    $objAmbUsuario = new AbmUsuario();
    $objUsuario = $objSession->getUsuario();
    if ($objUsuario->getUsDeshabilitado() == null) {
        $param["idUsuario"] = $objUsuario->getIdUsuario();
        // arreglo de objetos usarioRol, cada objeto tiene un objeto usuario y uno ROl
        $rolesUsuario = $objAmbUsuario->darRoles($param);
        $objMenuRol = new AbmMenuRol();
        if (count($rolesUsuario) > 1) {
            // poder elegir el rol que quiere mostrar el menu
            // creo todos los menus segun el rol y los guardo en un array
            $arrayMenu = [];
            foreach ($rolesUsuario as $rolUsua) {
                $idRol = $rolUsua->getObjRol()->getIdRol();
                $menuRol = $objMenuRol->darMenusPorRol($idRol);
                // guardo el arreglo asosiativo con la clave $idRol
                $arrayMenu[$idRol] = $menuRol;
            }
            $datos = data_submitted();
            if (isset($datos['rol'])) {
                $idSeleccionado = $datos['rol'];
                $menues = $arrayMenu[$idSeleccionado];
            } else {
                $idSeleccionado = $rolesUsuario[0]->getObjRol()->getIdRol();
                $menues = $arrayMenu[$idSeleccionado];
            }
        } else {
            $idSeleccionado = null;
            $menues = $objMenuRol->darMenusPorUsuario($objUsuario);
        }
    }else{
        $objSession->cerrar();
        header("Refresh: 0; URL='$VISTA/home/index.php'");
    }
} else {
    $idSeleccionado = null;
    $abmMenu = new AbmMenu();
    $array = [];
    $array["idMenu"] = 1;
    $menu = $abmMenu->buscar($array);
    $menues = [];
    array_push($menues, $menu[0]);
    $array["idMenu"] = 11;
    $menu = $abmMenu->buscar($array);
    array_push($menues, $menu[0]);
    $array["idMenu"] = 12;
    $menu = $abmMenu->buscar($array);
    array_push($menues, $menu[0]);
}

echo '
<nav class="navbar navbar-expand-lg  bg-dark  sticky-top" data-bs-theme="dark">
  <div class="container-fluid  ">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-center" id="navbarNavAltMarkup">
      <div class="navbar-nav ">
      ';
      foreach ($menues as $objMenu) {
        if ($objMenu->getMeDeshabilitado() == NULL) {
            $nombreMenu = $objMenu->getMeNombre();
            $seleccionado = ($pagSeleccionada == $nombreMenu) ? "link-underline-light link-underline-opacity-100" : "";
            echo
            '<h2 class="m-3 text-center">
            
                <a class="link-light link-offset-2 link-underline-opacity-0 link-underline-opacity-100-hover ' . $seleccionado . '" href="' . $objMenu->getMeDescripcion() . '?rol=' . $idSeleccionado . '">'
                . $objMenu->getMeNombre() .
                '</a>
                </h2>';
        }
    }
    if ($sesionValida) {
        echo
        '<form class="text-center" name="cerrarSesion" id="cerrarSesion" method="post" action=' . $VISTA . '/accion/eliminarSesion.php>
            <input class="m-3 p-2 btn btn-danger" type="submit" value="Logout">
            </form>';
    }
    if ($sesionValida) {
        if (count($rolesUsuario) > 1) {
            // poder elegir el rol que quiere mostrar el menu actualiza la pagina
            echo "<form id='seleccionRolesForm' class='text-center'accion='' method='GET'>";
            echo "<select class='m-3 p-2 text-center' name='rol'id='rol' onchange='submitForm(this.value)'> ";
            // echo "<option> Seleccione la vista del rol</option>";
            foreach ($rolesUsuario as $rolUs) {
                $rol = $rolUs->getObjRol();
                if ($idSeleccionado == $rol->getIdRol()) {
                    echo "<option selected value='" . $rol->getIdRol() . "'>" . $rol->getIdRol() . ": " . $rol->getRolDescripcion() . "</option>";
                } else {
                    echo "<option  value='" . $rol->getIdRol() . "'>" . $rol->getIdRol() . ": " . $rol->getRolDescripcion() . "</option>";
                }
            }
            echo "</select>";
            echo "</form>";
        }
    }
      echo'
      </div>
    </div>
  </div>
</nav>
';
?>
<script>
    function submitForm(idSeleccionado) {
        document.getElementById("seleccionRolesForm").submit();
        window.location.href = '<?=$VISTA?>/home/index.php?rol='+idSeleccionado;
    }
</script>