const tableLista = document.querySelector("#tableListaProductos tbody");
let productosjson = [];
document.addEventListener("DOMContentLoaded", function() {
    if (tableLista) {
        getListaProductos();
    }
    
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
            if (res.totalPaypal > 0) {
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
