let tblPendientes, tblFinalizados, tblProceso;

const myModal = new bootstrap.Modal(document.getElementById("modalPedidos"));

document.addEventListener("DOMContentLoaded", function() {
    tblPendientes = $("#tblPendientes").DataTable({
        columns: [
            { data: "id_transaccion" },
            { data: "monto" },
            { data: "estado" },
            { data: "fecha" },
            { data: "email" },
            { data: "nombre" },
            { data: "accion" },
        ],
        language,
        dom,
        buttons,
    });
    tblProceso = $("#tblProceso").DataTable({
        columns: [
            { data: "id_transaccion" },
            { data: "monto" },
            { data: "estado" },
            { data: "fecha" },
            { data: "email" },
            { data: "nombre" },
            { data: "accion" },
        ],
        language,
        dom,
        buttons,
    });
    tblFinalizados = $("#tblFinalizados").DataTable({
        columns: [
            { data: "id_transaccion" },
            { data: "monto" },
            { data: "estado" },
            { data: "fecha" },
            { data: "email" },
            { data: "nombre" },
            { data: "accion" },
        ],
        language,
        dom,
        buttons,
    });
});


