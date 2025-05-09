const btnAddcarrito = document.querySelectorAll(".btnAddcarrito");
const verCarrito = document.querySelector('#verCarrito');
const tableListaCarrito = document.querySelector('#tableListaCarrito tbody');
const btnAddDeseo = document.querySelectorAll(".btnAddDeseo");
const btnDeseo = document.querySelector('#btnCantidadDeseo');

let listaDeseo, listaCarrito;
document.addEventListener("DOMContentLoaded", function () {
    //NEW!!!
    if (localStorage.getItem('listaDeseo') != null) {
        listaDeseo = JSON.parse(localStorage.getItem('listaDeseo'));
    }
    if (localStorage.getItem("listaCarrito") != null) {
        listaCarrito = JSON.parse(localStorage.getItem("listaCarrito"));
    }
    //NEW!!!
    for (let i = 0; i < btnAddDeseo.length; i++) {
        btnAddDeseo[i].addEventListener('click', function () {
            let idProducto = btnAddDeseo[i].getAttribute('prod');
            agregarDeseo(idProducto);
        })        
    }
    for (let i = 0; i < btnAddcarrito.length; i++) {
        btnAddcarrito[i].addEventListener("click", function (e) {
            e.preventDefault();
            let idProducto = btnAddcarrito[i].getAttribute("prod");
            agregarCarrito(idProducto, 1);
        });
    }
    cantidadDeseo();
    

    verCarrito.addEventListener('click', function () {
        getListaCarrito();
        $('#modalCarrito').modal('show')
    })
});

//NEW!!!
//agregar productos a la lista de deseos
function agregarDeseo(idProducto, talla) {
    if (localStorage.getItem('listaDeseo') == null) {
        listaDeseo = [];
    } else {
        let listaExiste = JSON.parse(localStorage.getItem('listaDeseo'));
        for (let i = 0; i < listaExiste.length; i++) {
            if (listaExiste[i]['idProducto'] == idProducto) {
                Swal.fire(
                    'Aviso?',
                    'EL PRODUCTO YA ESTÁ EN TU LISTA DE DESEO',
                    'warning'
                );
                return;
            }            
        }
        listaDeseo.concat(localStorage.getItem('listaDeseo'));
    }

    listaDeseo.push({
        idProducto: idProducto,
        cantidad: 1,
        talla: talla,
    });
    localStorage.setItem('listaDeseo', JSON.stringify(listaDeseo));
    alertaPerzanalizada('Aviso?', 'PRODUCTO AGREGADO A LA LISTA DE DESEOS', 'success');
    cantidadDeseo();
}

function cantidadDeseo() {
    let listas = JSON.parse(localStorage.getItem("listaDeseo"));
    if (listas != null) {
        btnDeseo.textContent = listas.length;
    } else {
        btnDeseo.textContent = 0;
    }
}
//agregar productos al carrito
function agregarCarrito(idProducto, cantidad, talla, accion = false) {
    if (localStorage.getItem("listaCarrito") == null) {
        listaCarrito = [];
    } else {
        let listaExiste = JSON.parse(localStorage.getItem("listaCarrito"));
        for (let i = 0; i < listaExiste.length; i++) {
            //NEW!!!
            if (accion) {
                eliminarListaDeseo(idProducto);
            }
            if (listaExiste[i]["idProducto"] == idProducto) {
                alertaPerzanalizada("EL PRODUCTO YA ESTA AGREGADO", "warning")
                return;
            }
        }
        listaCarrito.concat(localStorage.getItem("listaCarrito"));
    }
    listaCarrito.push({
        idProducto: idProducto,
        cantidad: cantidad,
        talla: talla,
    });
    localStorage.setItem("listaCarrito", JSON.stringify(listaCarrito));
    alertaPerzanalizada("PRODUCTO AGREGADO AL CARRITO", "success")
    
}

//ver carrito
function getListaCarrito() {
    const miTalla = JSON.parse(localStorage.getItem('listaCarrito'));
    const url = base_url + 'principal/listaProductos';
    const http = new XMLHttpRequest();
    http.open('POST', url, true);
    http.send(JSON.stringify(listaCarrito));
    http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            let html = '';

            // Reemplazar el forEach con un bucle for
            for (let i = 0; i < res.productos.length; i++) {
                const producto = res.productos[i];
                html += `<tr>
                    <td>
                    <img class="img-thumbnail" src="${base_url + producto.imagen}" alt="" width="100">
                    </td>
                    <td>${producto.nombre}</td>
                    <td><span class="badge bg-warning">${res.moneda + ' ' + producto.precio}</span></td>
                    <td>${miTalla[i].talla}</td>
                    <td>${producto.subTotal}</td>
                </tr>`;
            }

            tableListaCarrito.innerHTML = html;
            document.querySelector('#totalGeneral').textContent = res.total + ' ' + res.moneda;
            
        }
    }
}
