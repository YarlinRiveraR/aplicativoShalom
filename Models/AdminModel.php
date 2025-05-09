<?php
class AdminModel extends Query{
 
    public function __construct()
    {
        parent::__construct();
    }
    public function getUsuario($correo)
    {
        $sql = "SELECT * FROM usuarios WHERE correo = '$correo'";
        return $this->select($sql);
    }
    public function getTotales($estado)
    {
        $sql = "SELECT COUNT(*) AS total FROM pedidos WHERE proceso = $estado";
        return $this->select($sql);
    }
    
    // Actualiza el token de recuperación para el usuario identificado por su correo
    public function updateToken($correo, $token) {
        $sql = "UPDATE usuarios SET token = ? WHERE correo = ?";
        $data = array($token, $correo);
        return $this->save($sql, $data);
    }

    public function getUserByToken($token) {
        $sql = "SELECT * FROM usuarios WHERE token = '$token' AND estado = 1";
        return $this->select($sql);
    }

    // Actualiza la contraseña del usuario identificado por su correo
    public function updateNewPassword($correo, $hashedPassword) {
        $sql = "UPDATE usuarios SET clave = ? WHERE correo = ?";
        $data = array($hashedPassword, $correo);
        return $this->save($sql, $data);
    }    

    public function clearToken($correo) {
        $sql = "UPDATE usuarios SET token = NULL WHERE correo = ?";
        $data = array($correo);
        return $this->save($sql, $data);
    }
}
 
?>