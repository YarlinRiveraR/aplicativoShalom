<?php
class ClientesModel extends Query{
 
    public function __construct()
    {
        parent::__construct();
    }
    public function getCategorias() {
        $sql = "SELECT * FROM categorias WHERE estado = 1";
        return $this->selectAll($sql);
    }
    public function registroDirecto($nombre, $correo, $clave, $token)
    {
        $sql = "INSERT INTO clientes (nombre, correo, clave, token) VALUES (?,?,?,?)";
        $datos = array($nombre, $correo, $clave, $token);
        $data = $this->insertar($sql, $datos);
        if ($data > 0) {
            $res = $data;
        } else {
            $res = 0;
        }
        return $res;
    }
    public function getToken($token)
    {
        $sql = "SELECT * FROM clientes WHERE token = '$token'";
        return $this->select($sql);
    }
    public function actualizarVerify($id)
    {
        $sql = "UPDATE clientes SET token=?, verify=? WHERE id=?";
        $datos = array(null, 1, $id);
        $data = $this->save($sql, $datos);
        if ($data == 1) {
            $res = $data;
        } else {
            $res = 0;
        }
        return $res;
    }
    public function getVerificar($correo)
    {
        $sql = "SELECT * FROM clientes WHERE correo = '$correo'";
        return $this->select($sql);
    }

    public function registrarPedido($id_transaccion, $monto, $estado, $fecha, $email,
    $nombre, $id_cliente)
    {
        $sql = "INSERT INTO pedidos (id_transaccion, monto, estado, fecha, email,
        nombre, id_cliente) VALUES (?,?,?,?,?,?,?)";
        $datos = array($id_transaccion, $monto, $estado, $fecha, $email,
        $nombre, $id_cliente);
        $data = $this->insertar($sql, $datos);
        if ($data > 0) {
            $res = $data;
        } else {
            $res = 0;
        }
        return $res;
    }

    public function getProducto($id_producto)
    {
        $sql = "SELECT * FROM productos WHERE id = $id_producto";
        return $this->select($sql);
    }

    //NEW!!!
    // Actualiza el token de recuperación para el cliente
    public function updateToken($correo, $token) {
        $sql = "UPDATE clientes SET token = ? WHERE correo = ?";
        $datos = array($token, $correo);
        return $this->save($sql, $datos);
    }

    // Obtiene el cliente a partir del token
    public function getClienteByToken($token) {
        $sql = "SELECT * FROM clientes WHERE token = ?";
        return $this->select($sql, [$token]);
    }

    // Actualiza la contraseña del cliente
    public function updatePassword($correo, $hashedPassword) {
        $sql = "UPDATE clientes SET clave = ? WHERE correo = ?";
        return $this->save($sql, [$hashedPassword, $correo]);
    }

    // Limpia el token (lo pone en NULL) para el cliente
    public function clearToken($correo) {
        $sql = "UPDATE clientes SET token = NULL WHERE correo = ?";
        return $this->save($sql, [$correo]);
    }
}
 
?>