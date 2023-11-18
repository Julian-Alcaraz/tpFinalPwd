function abrirModalProductos(idComprax, idCompraEstado) {
  console.log(idCompraEstado);
  var contenidoModal = document.getElementById('contenidoModalProductos');
  contenidoModal.innerHTML = '';
  j = 1;
  for (var i = 0; i < arregloItems.length; i++) {
    var compraItem = arregloItems[i];
    if (compraItem.objCompra.idCompra == idComprax) {
      mensaje = '<h3>Producto Numero: ' + j + '</h3>' +
        '<div class="cajaLista">' +
        '<div class="row align-items-center">' +
        '<div class="col "><p>Producto: ' + compraItem.objProducto.proNombre +
        '<p>Precio por Unidad: $' + compraItem.objProducto.proPrecio +
        '<p>Unidades: ' + compraItem.ciCantidad + '</div>';
      if (idCompraEstado == 3 || idCompraEstado == 4) {
        mensaje += '<div class="col"><button type="button" disabled class="btn btn-secondary" onclick="eliminarItem(' + compraItem.idCompraItem + ',' + idComprax + ')">Eliminar</button> </div></div></div>';
      } else {
        mensaje += '<div class="col"><button type="button" class="btn btn-danger" onclick="eliminarItem(' + compraItem.idCompraItem + ',' + idComprax + ')">Eliminar</button> </div></div></div>';
      }
      contenidoModal.innerHTML += mensaje
      j++;
    }
  }
  $("#productosModal").modal("show");
}

function abrirModalEstados(idComprax) {
  var contenidoModal = document.getElementById('contenidoModal');
  contenidoModal.innerHTML = '';
  j = 1;
  for (var i = 0; i < arregloCompraEstado.length; i++) {
    var compraEstado = arregloCompraEstado[i];
    if (compraEstado.objCompra.idCompra == idComprax) {
      contenidoModal.innerHTML += '<h3>ESTADO NUMERO:' + (j) + '</h3><div class="cajaLista">' +
        '<p> ID Compra : ' + compraEstado.objCompra.idCompra +
        '<p> ID tipo Estado: ' + compraEstado.objCompraEstadoTipo.idCompraEstadoTipo +
        '<p> DESCRIPCION: ' + compraEstado.objCompraEstadoTipo.cetDescripcion +
        '<p> FECHA INICIO: ' + compraEstado.ceFechaIni + ' ' +
        '<p> FECHA FIN: ' + compraEstado.ceFechaFin + '</div> </p> ';
      j++;
    }
  }
  $("#estadosModal").modal("show");
}

function eliminarItem(idCompraItem, idCompra) {
  $.ajax({
    type: "POST",
    url: "./accion/actualizarCompraItem.php",
    data: {
      idCompraItem: idCompraItem,
      idCompra: idCompra,
    },
    success: function (response) {
      accionSuccess();
    },
    error: function (error) {
      console.log("Error:", error);
    },
  });
}

function enviarDatos(idCompra, ultimoIdCompraEstado) {
  var idCompraEstadoTipo = $("#estado-" + idCompra).val();
  $.ajax({
    type: "POST",
    url: "./accion/actualizarEstado.php",
    data: {
      idCompraEstado: ultimoIdCompraEstado,
      idCompraEstadoTipo: idCompraEstadoTipo,
      idCompra: idCompra,
    },
    success: function (response) {
      accionSuccess();
    },
    error: function (error) {
      console.log("Error:", error);
    },
  });
}

function accionSuccess() {
  Swal.fire({
    icon: "success",
    title: "La accion se realizo correctamente!",
    showConfirmButton: false,
    timer: 1500,
  });
  setTimeout(function () {
    location.reload();
  }, 1500);
}

function accionFailure() {
  Swal.fire({
    icon: "error",
    title: "No se ha realizado la accion!",
    showConfirmButton: false,
    timer: 1500,
  });
  setTimeout(function () {
    location.reload();
  }, 1500);
}
