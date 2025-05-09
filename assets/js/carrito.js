const btnAddDeseo = document.querySelectorAll(".btnAddDeseo");
const btnDeseo = document.querySelector('#btnCantidadDeseo');

let listaDeseo, listaCarrito;
document.addEventListener("DOMContentLoaded", function () {
    //NEW!!!
    if (localStorage.getItem('listaDeseo') != null) {
        listaDeseo = JSON.parse(localStorage.getItem('listaDeseo'));
    }
    //NEW!!!
    for (let i = 0; i < btnAddDeseo.length; i++) {
        btnAddDeseo[i].addEventListener('click', function () {
            let idProducto = btnAddDeseo[i].getAttribute('prod');
            agregarDeseo(idProducto);
        })        
    }
    cantidadDeseo();
    
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