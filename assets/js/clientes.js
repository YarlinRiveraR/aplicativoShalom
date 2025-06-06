const tableLista = document.querySelector("#tableListaProductos tbody");
const tblPendientes = document.querySelector('#tblPendientes');
const totalEl    = document.querySelector("#totalProducto");
const frmComp    = document.getElementById("frmComprobante");
const btnEnviar  = document.getElementById("btnEnviarComprobante");
let productosjson = [];
const estadoEnviado = document.querySelector('#estadoEnviado');
const estadoProceso = document.querySelector('#estadoProceso');
const estadoCompletado = document.querySelector('#estadoCompletado');


document.addEventListener("DOMContentLoaded", function() {
     getListaProductos().then(res => {
        const lista = JSON.parse(localStorage.getItem('listaCarrito'))||[];
        const idHidden = document.getElementById('id_pedido').value;
        if (lista.length > 0 && !idHidden) {
        registrarPedido(res.totalRaw);
        }
    });
$('#tblPendientes').DataTable({
    ajax: {
        url: base_url + 'clientes/listarPendientes',
        dataSrc: ''
    },
    columns: [
        { data: 'id_transaccion' },
        { data: 'monto' },
        { data: 'fecha' },
        { 
            data: 'comprobante',
            render: function(data, type, row) {
                if (data) {
                    return `<a href="${base_url}assets/comprobantes/${data}" target="_blank" class="btn btn-sm btn-info">
                        <i class="fas fa-eye"></i> Ver
                    </a>`;
                } else {
                    return '<span class="badge bg-secondary">Pendiente</span>';
                }
            }
        },
        { data: 'accion' }
    ],
    language,
    dom,
    buttons
});
});

function getListaProductos() {
  return new Promise((resolve, reject) => {
    const lista = JSON.parse(localStorage.getItem('listaCarrito'))||[];
    const http  = new XMLHttpRequest();
    http.open('POST', base_url+'principal/listaProductos', true);
    http.setRequestHeader('Content-Type','application/json');
    http.onload = () => {
      if (http.status===200) {
        const res = JSON.parse(http.responseText);
        if (res.productos.length===0) {
          tableLista.innerHTML = `
            <tr><td colspan="6" class="text-center">CARRITO VACÍO</td></tr>`;
          totalEl.textContent = '';
        } else {
          let html='';
          for (let i=0; i<res.productos.length; i++){
            const p=res.productos[i];
            html+=`
              <tr>
                <td><img src="${p.imagen}" width="60"></td>
                <td>${p.nombre}</td>
                <td>${res.moneda} ${p.precio}</td>
                <td>${lista[i]?.talla||''}</td>
                <td>${p.cantidad}</td>
                <td>${res.moneda} ${p.subTotal}</td>
              </tr>`;
          }
          tableLista.innerHTML = html;
          totalEl.textContent = `TOTAL A PAGAR: ${res.moneda} ${res.total}`;
        }
        resolve(res);
      } else reject();
    };
    http.send(JSON.stringify(lista));
  });
}


function registrarPedido() {
    const url = base_url + 'clientes/registrarPedido';
    const http = new XMLHttpRequest();
    http.open('POST', url, true);
    http.setRequestHeader('Content-Type', 'application/json');

    const listaCarrito = JSON.parse(localStorage.getItem('listaCarrito')) || [];
    // Primero obtén el total
    const httpLista = new XMLHttpRequest();
    httpLista.open('POST', base_url + 'principal/listaProductos', true);
    httpLista.setRequestHeader('Content-Type', 'application/json');
    httpLista.send(JSON.stringify(listaCarrito));

    httpLista.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            const resTotal = JSON.parse(this.responseText);
            // Ahora registra el pedido
            const payload = {
                pedidos: { total: resTotal.totalRaw },
                productos: listaCarrito
            };
            http.send(JSON.stringify(payload));

            http.onreadystatechange = function() {
                if (this.readyState === 4 && this.status === 200) {
                    const res = JSON.parse(this.responseText);
                    console.log('registrarPedido response:', res);
                    if (res.icono === 'success') {
                        document.getElementById('id_pedido').value = res.id_pedido;
                    } else {
                        Swal.fire('Error', res.msg, 'error');
                    }
                }
            };
        }
    };
}

 btnEnviar.addEventListener('click', () => {
    const formData = new FormData(frmComp);
    const idPedido = formData.get('id_pedido');
    const archivo  = formData.get('comprobante');

    if (!idPedido) return Swal.fire('Error','Pedido no registrado aún','error');
    if (!archivo || archivo.size === 0) {
    return Swal.fire('¡Ups!','Selecciona un comprobante','warning');
}

const tiposPermitidos = ['image/jpeg', 'image/png', 'application/pdf'];
if (!tiposPermitidos.includes(archivo.type)) {
    return Swal.fire('Formato no válido', 'El comprobante debe ser JPG, PNG o PDF', 'error');
}


    btnEnviar.disabled = true;
    btnEnviar.textContent = 'Enviando…';

    fetch(base_url+'clientes/enviarComprobante',{ method:'POST', body: formData })
      .then(r=>r.json())
      .then(json => {
        if (json.icono==='success') {
            Swal.fire('¡Listo!','Comprobante enviado con éxito','success');
            localStorage.removeItem('listaCarrito');
           setTimeout(()=>{ // CAMBIO NUEVO
            location.reload();
           },5000)
            
        } else {
          Swal.fire('Error', json.msg, 'error');
        }
      })
      .catch(()=> Swal.fire('Error','No se pudo enviar','error'));
  });


let confirmarListenerAgregado = false;

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
                let precioFormato = parseFloat(row.precio);
                let subTotalFormato = subTotal;

                html += `<tr>
                    <td>${row.producto}</td>
                    <td><span class="badge bg-warning">${res.moneda + ' ' + precioFormato}</span></td>
                    <td><span class="badge bg-primary">${row.cantidad}</span></td>
                    <td>${subTotalFormato}</td>
                </tr>`;
            });

            document.querySelector('#tablePedidos tbody').innerHTML = html;

            // ✅ MOSTRAR BOTÓN "VER COMPROBANTE" SI EXISTE
            const contenedorBtn = document.querySelector('#contenedor-boton-comprobante');
            if (res.comprobante && res.comprobante.archivo) {
                contenedorBtn.innerHTML = `
                    <a href="${base_url}assets/comprobantes/${res.comprobante.archivo}" 
                       target="_blank" class="btn btn-success mt-3">
                        <i class="fas fa-file-alt"></i> Ver Comprobante
                    </a>`;
            } else {
                contenedorBtn.innerHTML = ''; // Oculta si no hay comprobante
            }

            mPedido.show();
        }
    }


    

    const formConfirmar = document.querySelector("#formConfirmar");

    if (formConfirmar && !confirmarListenerAgregado) {
        confirmarListenerAgregado = true;

        formConfirmar.addEventListener('submit', function (e) {
            e.preventDefault();

            const id_transaccion = document.querySelector("#id_transaccion").value;
            const imagenInput = document.querySelector("#imagen");

            if (imagenInput.files.length === 0) {
                Swal.fire("Error", "Por favor selecciona una imagen del comprobante.", "warning");
                return;
            }

            const formData = new FormData(formConfirmar);
            const url = base_url + 'clientes/enviarComprobante';

            const http = new XMLHttpRequest();
            http.open('POST', url, true);
            http.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    const res = JSON.parse(this.responseText);
                    Swal.fire("Aviso", res.msg, res.icono);

                    if (res.icono === 'success') {
                        document.querySelector("#modalConfirmar").classList.remove('show');
                        document.querySelector("#modalConfirmar").style.display = 'none';
                        document.body.classList.remove('modal-open');
                        document.querySelector(".modal-backdrop").remove();

                        $('#tblPendientes').DataTable().ajax.reload();
                    }
                }
            };
            http.send(formData);
        });
    }
}
