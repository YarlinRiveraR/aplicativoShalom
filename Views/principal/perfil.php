<?php include_once 'Views/template/header-secundario.php'; ?>

<!-- Start Content -->
<div class="container py-5">
  <?php if ($data['verificar']['verify'] == 1) { ?>
    <div class="row">
      <div class="col-3">
        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
          <button class="nav-link active" id="v-pills-home-tab" data-toggle="pill" data-target="#v-pills-home" type="button" role="tab" aria-controls="v-pills-home" aria-selected="true">Pagos</button>
          <button class="nav-link" id="v-pills-profile-tab" data-toggle="pill" data-target="#v-pills-profile" type="button" role="tab" aria-controls="v-pills-profile" aria-selected="false">Pedidos</button>
        </div>
      </div>
      <div class="col-9">
        <div class="tab-content" id="v-pills-tabContent">
          <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">
            <div class="row">
              <div class="col-md-12">
                <div class="card shadow-lg">
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-bordered table-striped table-hover align-middle" id="tableListaProductos">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Talla</th>
                            <th>Cantidad</th>
                            <th>SubTotal</th>
                          </tr>
                        </thead>
                        <tbody>
                        </tbody>
                      </table>
                    </div>
                  </div>

                  <div class="card-footer text-end">
                    <h2 id="totalProducto"></h2>
                  </div>

                  <!-- Formas de pago -->
                  <div class="card-body">
                    <style>
                      .methods,
                      .actions {
                        margin-bottom: 1rem;
                      }
                      .methods button {
                        background: none;
                        border: 1px solid #ccc;
                        width: 100%;
                        padding: .75rem;
                        margin-bottom: .5rem;
                        display: flex;
                        align-items: center;
                        cursor: pointer;
                      }
                      .methods img {
                        width: 24px;
                        margin-right: .5rem;
                      }
                    </style>
                    <div class="methods">
                      <h1>Formas de pago</h1>

                      <!-- Botón de Nequi -->
                      <button class="btn btn-light d-flex align-items-center mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#nequiPanel" aria-expanded="false" aria-controls="nequiPanel" style="border:1px solid #ccc; padding:.75rem; width:100%;">
                        <img src="<?php echo BASE_URL . 'assets/images/nequi-seeklogo.png'; ?>" style="width:150px;height:auto; margin-right:.5rem;" alt="Nequi">
                      </button>

                      <!-- Contenedor colapsable que carga la vista de Nequi -->
                      <div class="collapse mt-3" id="nequiPanel">
                        <?php include "Views/pagos/nequi.php" ?>
                      </div>

                      <!-- Botón de Davivienda -->
                      <button class="btn btn-light d-flex align-items-center mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#daviviendaPanel" aria-expanded="false" aria-controls="daviviendaPanel" style="border:1px solid #ccc; padding:.75rem; width:100%;">
                        <img src="<?php echo BASE_URL . 'assets/images/davivienda-seeklogo.png'; ?>" style="width:150px; height:auto; margin-right:.5rem;" alt="Davivienda">
                      </button>

                      <!-- Contenedor colapsable para Davivienda -->
                      <div class="collapse mt-3" id="daviviendaPanel">
                        <?php include __DIR__ . '/../pagos/Davivienda.php'; ?>
                      </div>

                      <!-- Botón de Daviplata -->
                      <button class="btn btn-light d-flex align-items-center mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#daviplataPanel" aria-expanded="false" aria-controls="daviplataPanel" style="border:1px solid #ccc; padding:.75rem; width:100%;">
                        <img src="<?php echo BASE_URL . 'assets/images/daviplata-seeklogo.png'; ?>" style="width:150px; height:auto; margin-right:.5rem;" alt="DaviPlata">
                      </button>

                      <!-- Contenedor colapsable para DaviPlata -->
                      <div class="collapse mt-3" id="daviplataPanel">
                        <?php include __DIR__ . '/../pagos/Daviplata.php'; ?>
                      </div>

                      <!-- Formulario de comprobante -->
                      <form id="frmComprobante" enctype="multipart/form-data">
                        <input type="hidden" id="id_pedido" name="id_pedido" value="">
                        
                        <div class="mb-3">
                          <label for="comprobante" class="form-label">Cargar comprobante (JPG, PNG o PDF - máx 2MB)</label>
                          <input id="comprobante" type="file" class="form-control" name="comprobante" accept="image/jpeg,image/png,application/pdf" required>
                        </div>
                        
                        <div class="actions">
                          <button type="button" id="btnEnviarComprobante" class="btn btn-primary">Enviar Comprobante</button>
                        </div>
                      </form>
                      
                      <style>
                        .mb-3 {
                          display: flex;
                          flex-direction: column;
                          align-items: center;
                        }
                        .mb-3 > label.form-label {
                          font-size: 1.25rem;
                          font-weight: 600;
                          margin-bottom: 0.5rem;
                        }
                        input[type="file"].form-control::-webkit-file-upload-button {
                          background-color: #ff80bf;
                          color: white;
                          border: none;
                          padding: 0.375rem 0.75rem;
                          border-radius: 0.25rem;
                          cursor: pointer;
                        }
                        input[type="file"].form-control::-moz-file-upload-button {
                          background-color: #ff80bf;
                          color: white;
                          border: none;
                          padding: 0.375rem 0.75rem;
                          border-radius: 0.25rem;
                          cursor: pointer;
                        }
                        .actions {
                          display: flex;
                          justify-content: center;
                          margin-top: 1rem;
                        }
                        #btnEnviarComprobante {
                          background-color: #ff80bf;
                          border: none;
                          color: white;
                          padding: 0.5rem 1.5rem;
                          border-radius: 0.25rem;
                          cursor: pointer;
                          font-size: 1rem;
                          width: auto;
                          transition: background-color 0.3s ease;
                          text-align: center;
                        }
                        #btnEnviarComprobante:hover {
                          background-color: #e669b8;
                        }
                      </style>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="col-md-12">
                <div class="card shadow-lg">
                  <div class="card-body text-center">
                    <img class="img-thumbnail rounded-circle" src="<?php echo BASE_URL . 'assets/img/logo.png'; ?>" alt="" width="100">
                    <hr>
                    <p><?php echo $_SESSION['nombreCliente']; ?></p>
                    <p><i class="fas fa-envelope"></i> <?php echo $_SESSION['correoCliente']; ?></p>

                    <div class="flex-column-perfil">
                      <a class="btn btn-danger" href="<?php echo BASE_URL . 'clientes/salir'; ?>"><i class="fas fa-times-circle"></i> Cerrar Sesión</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
            <div class="col-12">
              <div class="card shadow-lg">
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="tblPendientes" style="width: 100%;">
                      <thead class="bg-dark text-white">
                        <tr>
                          <th>#</th>
                          <th>Monto</th>
                          <th>Fecha</th>
                          <th>Comprobante</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php } else { ?>
    <div class="alert alert-danger text-center" role="alert">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
      </svg>
      <div class="h3">
        VERIFICA TU CORREO ELECTRONICO
      </div>
    </div>
  <?php } ?>
</div>
<!-- End Content -->

<div id="modalPedido" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Estado del Pedido</h5>
        <button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6 col-lg-4 pb-5">
            <div class="h-100 py-5 bg-info shadow" id="estadoEnviado">
              <div class="h1 text-util text-center"><i class="fa fa-truck fa-lg"></i></div>
              <h2 class="h5 mt-4 text-center">Pendiente</h2>
            </div>
          </div>

          <div class="col-md-6 col-lg-4 pb-5">
            <div class="h-100 py-5 bg-info shadow" id="estadoProceso">
              <div class="h1 text-util text-center"><i class="fa fa-exchange-alt"></i></div>
              <h2 class="h5 mt-4 text-center">Proceso</h2>
            </div>
          </div>

          <div class="col-md-6 col-lg-4 pb-5">
            <div class="h-100 py-5 bg-info shadow" id="estadoCompletado">
              <div class="h1 text-util text-center"><i class="fa fa-percent"></i></div>
              <h2 class="h5 mt-4 text-center">Completado</h2>
            </div>
          </div>
          <div class="col-md-12">
            <div class="table-responsive">
              <table class="table table-borderer table-striped table-hover align-middle" id="tablePedidos" style="width: 100%;">
                <thead>
                  <tr>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>SubTotal</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once 'Views/template/footer-secundario.php'; ?>

<script type="text/javascript" src="<?php echo BASE_URL . 'assets/DataTables/datatables.min.js'; ?>"></script>
<script src="<?php echo BASE_URL; ?>assets/js/es-ES.js"></script>
<script src="<?php echo BASE_URL . 'assets/js/clientes.js'; ?>"></script>
<script src="<?php echo BASE_URL . 'assets/js/helper.js'; ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>