<?php

class PedidosModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getPedidos($proceso)
    {
        $sql = "SELECT * FROM pedidos WHERE proceso = $proceso";
        return $this->selectAll($sql);
    }

    public function actualizarEstado($proceso, $idPedido)
    {
        $sql = "UPDATE pedidos SET proceso=? WHERE id = ?";
        $array = array($proceso, $idPedido);
        return $this->save($sql, $array);
    }

    // ✅ NUEVO: Guarda comprobante y cambia estado
    public function actualizarComprobante($idPedido, $ruta, $nuevoEstado)
    {
        $sql = "UPDATE pedidos SET comprobante = ?, proceso = ? WHERE id = ?";
        $datos = [$ruta, $nuevoEstado, $idPedido];
        return $this->save($sql, $datos);
    }

    // ✅ NUEVO: Contar pedidos por estado (pendiente, proceso, finalizado)
    public function contarPorEstado($estado)
    {
        $sql = "SELECT COUNT(*) AS total FROM pedidos WHERE proceso = ?";
        return $this->select($sql, [$estado]);
    }

}
