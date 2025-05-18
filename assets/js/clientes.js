const tableLista = document.querySelector("#tableListaProductos tbody");
const tblPendientes = document.querySelector('#tblPendientes');
let productosjson = [];
const estadoEnviado = document.querySelector('#estadoEnviado');
const estadoProceso = document.querySelector('#estadoProceso');
const estadoCompletado = document.querySelector('#estadoCompletado');
document.addEventListener("DOMContentLoaded", function() {
    if (tableLista) {
        getListaProductos();
    }
    //cargar datos pendientes con DataTables
    $('#tblPendientes').DataTable({
        ajax: {
            url: base_url + 'clientes/listarPendientes',
            dataSrc: ''
        },
        columns: [
            { data: 'id_transaccion' },
            { data: 'monto' },
            { data: 'fecha' },
            { data: 'accion' }
        ],
        language,
        dom,
        buttons

    });
    
});

function getListaProductos() {
    const miTalla = JSON.parse(localStorage.getItem('listaCarrito'));
    let html = '';
    const url = base_url + 'principal/listaProductos';
    const http = new XMLHttpRequest();
    http.open('POST', url, true);
    http.send(JSON.stringify(listaCarrito));
    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            if (res.productos && res.productos.length > 0) {
                // Reemplazar el forEach con un bucle for
                for (let i = 0; i < res.productos.length; i++) {
                    const producto = res.productos[i];
                    html += `<tr>
                        <td>
                            <img class="img-thumbnail rounded-circle" src="${producto.imagen}" alt="" width="100">
                        </td>
                        <td>${producto.nombre}</td>
                        <td><span class="badge bg-warning">${res.moneda + ' ' + producto.precio}</span></td>
                        <td>${miTalla[i].talla}</td>
                        <td><span class="badge bg-primary"><h3>${producto.cantidad}</h3></span></td>
                        <td>${producto.subTotal}</td>
                    </tr>`;
                    
                }
                
                tableLista.innerHTML = html;
                document.querySelector('#totalProducto').textContent = 'TOTAL A PAGAR: ' + res.total + ' ' + res.moneda;
            } else {
                tableLista.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center">CARRITO VACIO</td>
                </tr>
                `;
            }
        }
    }
}

function registrarPedido() {
    const url = base_url + 'clientes/registrarPedido';
    const http = new XMLHttpRequest();
    http.open('POST', url, true);

    // Obtener la lista de productos y el total
    const listaCarrito = JSON.parse(localStorage.getItem('listaCarrito')) || [];
    const httpListaProductos = new XMLHttpRequest();
    httpListaProductos.open('POST', base_url + 'principal/listaProductos', true);
    httpListaProductos.setRequestHeader('Content-Type', 'application/json');
    httpListaProductos.send(JSON.stringify(listaCarrito));

    httpListaProductos.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            http.send(JSON.stringify({
                pedidos: {
                    total: res.totalRaw
                },
                productos: listaCarrito
            }));

            http.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    console.log(this.responseText);
                    const res = JSON.parse(this.responseText);
                    Swal.fire("Aviso?", res.msg, res.icono);
                    if (res.icono == 'success') {
                        localStorage.removeItem('listaCarrito');
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    }
                }
            }
        }
    }
}

btnFinalizarPago.addEventListener('click', function() {
    // Obtener el carrito actual directamente desde localStorage
    const carrito = localStorage.getItem('listaCarrito') ? JSON.parse(localStorage.getItem('listaCarrito')) : [];
    if (carrito.length === 0) {
        Swal.fire('Aviso?', 'El carrito está vacío. Agrega al menos un producto antes de finalizar tu pedido.', 'warning');
        return;
    }

    Swal.fire({
        title: '¿Estás seguro?',
        text: "Este proceso es irreversible",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, confirmar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            registrarPedido();
        }
    });
});

function verPedido(idPedido) {
    estadoEnviado.classList.remove('bg-info');
    estadoProceso.classList.remove('bg-info');
    estadoCompletado.classList.remove('bg-info');
    const mPedido = new bootstrap.Modal(document.getElementById('modalPedido'));
    const url = base_url + 'clientes/verPedido/' + idPedido;
    const http = new XMLHttpRequest();
    http.open('GET', url, true);
    http.send();
    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            let html = '';
            if (res.pedido.proceso == 1) {
                estadoEnviado.classList.add('bg-info');
            } else if (res.pedido.proceso == 2) {
                estadoProceso.classList.add('bg-info');
            } else {
                estadoCompletado.classList.add('bg-info');
            }
            res.productos.forEach(row => {
                let subTotal = parseFloat(row.precio) * parseInt(row.cantidad);
                html += `<tr>
                    <td>${row.producto}</td>
                    <td><span class="badge bg-warning">${res.moneda + ' ' + row.precio}</span></td>
                    <td><span class="badge bg-primary">${row.cantidad}</span></td>
                    <td>${subTotal.toFixed(2)}</td>
                </tr>`;
            });
            document.querySelector('#tablePedidos tbody').innerHTML = html;
            mPedido.show();
        }
    }

}
