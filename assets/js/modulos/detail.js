const idProducto =  document.querySelector('#idProducto');
const talla =  document.querySelector('#idTalla');
//NEW!!!
const btnAddWish = document.querySelector('#btnAddWish');

document.addEventListener("DOMContentLoaded", function () {
    //Botón para la lista de deseos
    btnAddWish.addEventListener('click', function () {
        agregarDeseo(idProducto.value, talla.value);
    });
});