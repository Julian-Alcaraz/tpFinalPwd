<?php
include_once("../../config.php");
$pagSeleccionada = "Gestionar Compras";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once($ESTRUCTURA . "/header.php"); ?>
    <?php include_once($ESTRUCTURA . "/cabeceraBD.php");
    if ($objSession->validar()) {
        $tienePermiso = $objSession->tienePermisoB($objSession->getUsuario());
        if (!$tienePermiso) {
            header("Refresh: 0; URL='$VISTA/acceso/login.php'");
        }
        // agreegar para todas las paginas 
        $estadoPagina = $objSession->estadoMenu();
        if (!$estadoPagina) {
            header("Refresh: 0; URL='$VISTA/home/index.php'");
        }
    } else {
        header("Refresh: 0; URL='$VISTA/acceso/login.php'");
    } ?>
</head>

<body>
    <div id="contenido-perfil">
        <div style="margin-bottom: 80px;">
            <div class="container text-center p-4 mt-3 cajaLista">
                <h2>Lista de Compras </h2>
                <div class="table-responsive">
                    <table class="table  m-auto">
                        <thead class="table-dark fw-bold">
                            <tr>
                                <!--  <th scope="col">Productos Compra</th> Imagen, nombre, cantidad comprada--->
                                <!--   <th scope="col">Total por producto</th> Imagen, nombre, cantidad comprada--->
                                <th scope="col">IdCompra</th> <!--IdCOmpra--->
                                <th scope="col">Fecha de la compra</th>
                                <th scope="col">Nombre del usuario</th>
                                <th scope="col">Precio Total</th>
                                <th scope="col">Estado compra</th> <!--Muestra el estado, iniciada/cancelada/finalizada/etc--->
                                <th scope="col">Productos</th>
                                <th scope="col">Historial Estados</th> <!--Muestra el estado, iniciada/cancelada/finalizada/etc--->
                                <th scope="col">Acciones</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $objCompraEstadoTipo = new AbmCompraEstadoTipo();
                            $objCompra = new AbmCompra();
                            $objProducto = new AbmProducto();
                            $objCompraEstado = new AbmCompraEstado();
                            $objCompraItem = new AbmCompraItem();

                            $listadoProducto = $objProducto->buscar(null);
                            $listadoCompra = $objCompra->buscar(null);
                            $listadoCompraEstadoTipo = $objCompraEstadoTipo->buscar(null);
                            $listaCompraEstado = $objCompraEstado->buscar(null);
                            $listadoCompraItem = $objCompraItem->buscar(null);

                            // Desmonto los arreglos de objetos, con la funcion creada, estos objetos tienen a su vez otros objetos que tienen otros objetos
                            $arrayJsonCompraEstado = dismountList_ObjwObjwobj($listaCompraEstado);
                            $JsonListaCompraEstado = json_encode($arrayJsonCompraEstado, JSON_PRETTY_PRINT);
                            $arrayJsonCompraItem = dismountList_ObjwObjwobj($listadoCompraItem);
                            $JsonListaCompraItem = json_encode($arrayJsonCompraItem, JSON_PRETTY_PRINT);
                            foreach ($listadoCompra as $compra) {
                                echo '<tr>';
                                $total = 0;
                                foreach ($listadoProducto as $producto) {
                                    foreach ($listadoCompraItem as $item) {
                                        if ($item->getObjCompra()->getIdCompra() == $compra->getIdCompra()) {
                                            if ($item->getObjProducto()->getIdProducto() == $producto->getIdProducto()) {
                                                //  echo  $item->getObjProducto()->getProNombre() . "$" . $item->getObjProducto()->getProPrecio(). " x ".$item->getCiCantidad(). "<br>";
                                                $total += $item->getObjProducto()->getProPrecio() * $item->getCiCantidad();
                                                // echo "$" . $item->getObjProducto()->getProPrecio() * $item->getCiCantidad() ;                            
                                            }
                                        }
                                    }
                                }
                                echo '<td>' .  $compra->getIdCompra() . '</td>';
                                echo '<td>' . $compra->getCoFecha() . '</td>';
                                echo '<td>' . $compra->getObjUsuario()->getUsNombre() . '</td>';
                                echo '<td>' . $total . '</td>';
                                echo '<td>';
                                foreach ($listaCompraEstado as $estado) {
                                    if ($estado->getObjCompra()->getIdCompra() == $compra->getIdCompra()) {
                                        $objUltimoEstadoCompra = $estado;
                                        $ultimoIdCompraEstado = $estado->getIdCompraEstado();
                                    }
                                }
                                echo  "Estado: <strong>" . $objUltimoEstadoCompra->getObjCompraEstadoTipo()->getCetDescripcion() . '</strong><br>';
                                echo   "Fecha Inicio estado: " . $objUltimoEstadoCompra->getceFechaIni() . '<br>';
                                echo  "Fecha fin de estado: " . $objUltimoEstadoCompra->getceFechaFin() . '<br>';
                                echo '</td>';
                                echo '<td>';
                                echo '<button type="button" class="btn btn-primary" onclick="abrirModalProductos(' . $compra->getIdCompra() . ',' . $objUltimoEstadoCompra->getObjCompraEstadoTipo()->getIdCompraEstadoTipo() . ')"> Ver prod</button>';
                                echo '</td>';
                                echo '<td>';
                                echo '<button type="button" class="btn btn-primary" onclick="abrirModalEstados(' . $compra->getIdCompra() . ')">historial</button>';
                                echo '</td>';
                                echo '<td>' .
                                    '<form id="formSelect">' .
                                    '<select name="estado" id="estado-' . $compra->getIdCompra() . '">';
                                foreach ($listadoCompraEstadoTipo as $estadoTipo) {
                                    echo '<option value=" ' . $estadoTipo->getIdCompraEstadoTipo() . '"> ' . $estadoTipo->getCetDescripcion() . '</option>';
                                }
                                echo '</select>';
                                echo '<button type="button" class="mx-1 btn btn-primary" onclick="enviarDatos(' . $compra->getIdCompra() . ',\'' . $ultimoIdCompraEstado . '\',' . ')">Guardar</button>';
                                echo '</form>';
                                echo '</td>';
                                echo '</tr>';
                            }
                            //codigo va aca 
                            ?>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Modal mostrar estados  -->
        <div class="modal fade" id="estadosModal" name="estadosModal" tabindex="-1" aria-labelledby="editarModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form name="editarForm" id="editarForm" method="post">
                    <div class="modal-content">
                        <div class="modal-header bg-dark text-light">
                            <h1 class="modal-title fs-5" id="editarModalLabel">Historial Estados</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center text-black ">
                            <div name="contenidoModal" id="contenidoModal"></div>
                        </div>
                        <div class="modal-footer  bg-dark">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Modal mostrar Productos  -->
        <div class="modal fade" id="productosModal" name="productosModal" tabindex="-1" aria-labelledby="editarModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form name="editarForm" id="editarForm" method="post">
                    <div class="modal-content">
                        <div class="modal-header bg-dark text-light">
                            <h1 class="modal-title fs-5" id="editarModalLabel">Productos de la Compra</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center text-black ">
                            <div name="contenidoModalProductos" id="contenidoModalProductos"></div>
                        </div>
                        <div class="modal-footer  bg-dark">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php
    include_once($ESTRUCTURA . "/pie.php"); ?>
    <script>
        //paso los arreglos en formato json, a variables js para usar en el script
        var arregloItems = <?php echo $JsonListaCompraItem; ?>;
        var arregloCompraEstado = <?php echo $JsonListaCompraEstado; ?>;
    </script>
    <script src="js/accionesCompra.js"></script>
</body>


</html>