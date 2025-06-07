<?php

class Pedidos extends Controller
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        if (empty($_SESSION['nombre_usuario'])) {
            header('Location: '. BASE_URL . 'admin');
            exit;
        }
    }
    public function index()
    {
        $data['title'] = 'pedidos';
        $this->views->getView('admin/pedidos', "index", $data);
    }

    //Lista los pedidos iniciados que hicieron los clientes
    private function formatearPedidos($estado)
    {
        $data = $this->model->getPedidos($estado);
        foreach ($data as &$pedido) {
            // Siempre mostramos el botón de ver
            $acciones  = '<button class="btn btn-success" type="button" onclick="verPedido(' . $pedido['id'] . ')">'
                    .  '<i class="fas fa-eye"></i></button>';

            // Si no está en el último estado (3), añadimos el botón de avanzar al siguiente
            if ($estado < 3) {
                $siguiente = $estado + 1;
                $acciones .= ' <button class="btn btn-info" type="button" '
                        .  'onclick="cambiarProceso(' . $pedido['id'] . ', ' . $siguiente . ')">'
                        .  '<i class="fas fa-check-circle"></i></button>';
            }

            $pedido['accion'] = '<div class="d-flex">' . $acciones . '</div>';

            // Comprobante
            if (!empty($pedido['comprobante'])) {
                $url = BASE_URL . 'assets/comprobantes/' . $pedido['comprobante'];
                $pedido['comprobante'] = '<a href="' . $url . '" target="_blank" class="btn btn-sm btn-primary">Ver</a>';
            } else {
                $pedido['comprobante'] = '<span class="badge bg-secondary">No enviado</span>';
            }
        }

        return $data;
    }

public function listarPedidos()
{
    echo json_encode($this->formatearPedidos(1));
    die();
}

public function listarProceso()
{
    echo json_encode($this->formatearPedidos(2));
    die();
}

public function listarFinalizados()
{
    echo json_encode($this->formatearPedidos(3));
    die();
}
public function update($datos)
{
    $array = explode(',', $datos);
    if (count($array) !== 2 || !is_numeric($array[0]) || !is_numeric($array[1])) {
        echo json_encode(['msg' => 'Datos inválidos', 'icono' => 'error']);
        die();
    }

    [$idPedido, $proceso] = $array;
    $data = $this->model->actualizarEstado($proceso, $idPedido);
    if ($data == 1) {
        $respuesta = ['msg' => 'Pedido actualizado', 'icono' => 'success'];
    } else {
        $respuesta = ['msg' => 'Error al actualizar', 'icono' => 'error'];
    }
    echo json_encode($respuesta);
    die();
}


}
