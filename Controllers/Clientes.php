<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

class Clientes extends Controller
{
    public function __construct()
    {
        parent::__construct();
        session_start();
    }

    //mostrar el perfil del cliente
    public function index()
    {
        if (empty($_SESSION['correoCliente'])) {
            header('Location: ' . BASE_URL);
        }
        $data['perfil'] = 'si';
        $data['title'] = 'Tu Perfil';
        $data['categorias'] = $this->model->getCategorias();
        $data['verificar'] = $this->model->getVerificar($_SESSION['correoCliente']);
        $this->views->getView('principal', "perfil", $data);
    }

    // registrar un cliente directamente si aún no tiene cuenta
    public function registroDirecto()
    {
        if (isset($_POST['nombre']) && isset($_POST['clave'])) {
            if (empty($_POST['nombre']) || empty($_POST['correo']) || empty($_POST['clave'])) {
                $mensaje = array('msg' => 'TODO LOS CAMPOS SON REQUERIDOS', 'icono' => 'warning');
            } else {
                $nombre = $_POST['nombre'];
                $correo = $_POST['correo'];
                $clave = $_POST['clave'];
                $verificar = $this->model->getVerificar($correo);
                if (empty($verificar)) {
                    $token = md5($correo);
                    $hash = password_hash($clave, PASSWORD_DEFAULT);
                    $data = $this->model->registroDirecto($nombre, $correo, $hash, $token);
                    if ($data > 0) {
                        $_SESSION['idCliente'] = $data;
                        $_SESSION['correoCliente'] = $correo;
                        $_SESSION['nombreCliente'] = $nombre;
                        $mensaje = array('msg' => 'registrado con éxito', 'icono' => 'success', 'token' => $token);
                    } else {
                        $mensaje = array('msg' => 'error al registrarse', 'icono' => 'error');
                    }
                } else {
                    $mensaje = array('msg' => 'YA TIENES UNA CUENTA', 'icono' => 'warning');
                }
            }
            echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
            die();
        }
    }

    //enviar un correo de verificación al cliente
    public function enviarCorreo()
    {
        if (isset($_POST['correo']) && isset($_POST['token'])) {
            $mail = new PHPMailer(true);
            try {
                //Configuración del servidor
                $mail->SMTPDebug = 0;                      //Habilitar salida detallada para depuración
                $mail->isSMTP();                                            //Enviar utilizando SMTP
                $mail->Host       = HOST_SMTP;                     //Establecer el servidor SMTP para enviar a través de él
                $mail->SMTPAuth   = true;                                   //Habilitar autenticación SMTP
                $mail->Username   = USER_SMTP;                     //Nombre de usuario SMTP
                $mail->Password   = PASS_SMTP;                               //Contraseña SMTP
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Habilitar cifrado TLS implícito
                $mail->Port       = PUERTO_SMTP;                                    //Puerto TCP para conectarse; usa 587 si has configurado `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

                $mail->CharSet = 'UTF-8';

                //Destinatarios
                $mail->setFrom('pijamas.shalom.notificaciones@gmail.com', TITLE);
                $mail->addAddress($_POST['correo']);

                //Contenido
                $mail->isHTML(true);                                  //Establecer formato de correo como HTML
                $mail->Subject = 'Mensaje desde la: ' . TITLE;
                $mail->Body    = 'Para verificar tu correo en nuestra tienda <a href="' . BASE_URL . 'clientes/verificarCorreo/' . $_POST['token'] . '">CLIC AQUÍ</a>';
                $mail->AltBody = 'GRACIAS POR LA PREFERENCIA';

                $mail->send();
                $mensaje = array('msg' => 'CORREO ENVIADO, REVISA TU BANDEJA DE ENTRADA - SPAN', 'icono' => 'success');
            } catch (Exception $e) {
                $mensaje = array('msg' => 'ERROR AL ENVIAR CORREO: ' . $mail->ErrorInfo, 'icono' => 'error');
            }
        } else {
            $mensaje = array('msg' => 'ERROR FATAL: ', 'icono' => 'error');
        }
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        die();
    }

    //verificar el correo del cliente mediante un token
    public function verificarCorreo($token)
    {
        $verificar = $this->model->getToken($token);
        if (!empty($verificar)) {
            $this->model->actualizarVerify($verificar['id']);
            header('Location: ' . BASE_URL . 'clientes');
        }
    }

    //manejar el inicio de sesión directo de un cliente
    public function loginDirecto()
    {
        if (isset($_POST['correoLogin']) && isset($_POST['claveLogin'])) {
            if (empty($_POST['correoLogin']) || empty($_POST['claveLogin'])) {
                $mensaje = array('msg' => 'TODO LOS CAMPOS SON REQUERIDOS', 'icono' => 'warning');
            } else {
                $correo = $_POST['correoLogin'];
                $clave = $_POST['claveLogin'];
                $verificar = $this->model->getVerificar($correo);
                if (!empty($verificar)) {
                    if (password_verify($clave, $verificar['clave'])) {
                        $_SESSION['idCliente'] = $verificar['id'];
                        $_SESSION['correoCliente'] = $verificar['correo'];
                        $_SESSION['nombreCliente'] = $verificar['nombre'];
                        $mensaje = array('msg' => 'OK', 'icono' => 'success');
                    } else {
                        $mensaje = array('msg' => 'CONTRASEÑA INCORRECTA', 'icono' => 'error');
                    }
                } else {
                    $mensaje = array('msg' => 'EL CORREO NO EXISTE', 'icono' => 'warning');
                }
            }
            echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
            die();
        }
    }
    
    //NEW!!!
    // Recuperar contraseña (correo)
    public function sendRecovery() {
        if (isset($_POST['email']) && !empty($_POST['email'])) {
            $correo = $_POST['email'];
            $cliente = $this->model->getVerificar($correo);
            if (!empty($cliente)) {
                $token = md5(uniqid(rand(), true));
                $update = $this->model->updateToken($correo, $token);
                if ($update) {
                    $mail = new PHPMailer(true);
                    try {
                        $mail->SMTPDebug = 0;
                        $mail->isSMTP();
                        $mail->Host       = HOST_SMTP;
                        $mail->SMTPAuth   = true;
                        $mail->Username   = USER_SMTP;
                        $mail->Password   = PASS_SMTP;
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                        $mail->Port       = PUERTO_SMTP;

                        $mail->CharSet = 'UTF-8';

                        $mail->setFrom('pijamas.shalom.notificaciones@gmail.com', TITLE);
                        $mail->addAddress($correo);

                        $mail->isHTML(true);
                        $mail->Subject = 'Recuperación de Contraseña - ' . TITLE;
                        $mail->Body    = 'Para recuperar tu contraseña, haz clic en el siguiente enlace: <a href="' . BASE_URL . '?resetToken=' . $token . '">Recuperar Contraseña</a>';
                        $mail->AltBody = 'Para recuperar tu contraseña, visita: ' . BASE_URL . '?resetToken=' . $token;

                        $mail->send();
                        $mensaje = array('msg' => 'Correo enviado. Revisa tu bandeja de entrada.', 'icono' => 'success');
                    } catch (Exception $e) {
                        $mensaje = array('msg' => 'Error al enviar correo: ' . $mail->ErrorInfo, 'icono' => 'error');
                    }
                } else {
                    $mensaje = array('msg' => 'Error al actualizar el token.', 'icono' => 'error');
                }
            } else {
                $mensaje = array('msg' => 'El correo no existe.', 'icono' => 'warning');
            }
        } else {
            $mensaje = array('msg' => 'El correo es requerido.', 'icono' => 'warning');
        }
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Permite al cliente restablecer su contraseña usando el token recibido por correo
    public function resetPassword($token){
        $cliente = $this->model->getClienteByToken($token);
        if (empty($cliente)) {
            header('Location: ' . BASE_URL . '?msg=token_invalido');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (empty($_POST['new_password']) || empty($_POST['confirm_password'])) {
                $mensaje = array('msg' => 'Todos los campos son requeridos.', 'icono' => 'warning');
                echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
                die();
            }
            $newPassword = $_POST['new_password'];
            $confirmPassword = $_POST['confirm_password'];
            if ($newPassword !== $confirmPassword) {
                $mensaje = array('msg' => 'Las contraseñas no coinciden.', 'icono' => 'warning');
                echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
                die();
            }
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $update = $this->model->updatePassword($cliente['correo'], $hash);
            if ($update) {
                $this->model->clearToken($cliente['correo']);
                $mensaje = array('msg' => 'Contraseña actualizada', 'icono' => 'success');
            } else {
                $mensaje = array('msg' => 'Error al actualizar la contraseña. Inténtalo de nuevo.', 'icono' => 'error');
            }
            echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
            die();
        } else {
            // Para solicitudes GET, redirigimos o devolvemos un error
            $mensaje = array('msg' => 'Método no permitido', 'icono' => 'error');
            echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
            die();
        }
    }

    //registrar pedidos realizados por un cliente
    public function registrarPedido()
    {
        $datos = file_get_contents('php://input');
        $json = json_decode($datos, true);
        $pedidos = $json['pedidos'];
        $productos = $json['productos'];
        $total = $json['pedidos']['total'];
        if (is_array($pedidos) && is_array($productos)) {

            $monto = $total; // Total del pedido calculado en el frontend


            $id_transaccion = uniqid();
            // $monto = $pedidos['purchase_units'][0]['amount']['value'];
            $estado = "COMPLETED";
            $fecha = date('Y-m-d H:i:s');
            $email = $_SESSION['correoCliente'];
            $nombre = $_SESSION['nombreCliente'];
            $id_cliente = $_SESSION['idCliente'];
            $data = $this->model->registrarPedido(
                $id_transaccion,
                $monto,
                $estado,
                $fecha,
                $email,
                $nombre,
                $id_cliente
            );
            if ($data > 0) {
                foreach ($productos as $producto) {
                    $temp = $this->model->getProducto($producto['idProducto']);
                    $this->model->registrarDetalle($temp['nombre'], formatearMoneda($temp['precio']), $producto['cantidad'], $data, $producto['idProducto']);
                }
                $mensaje = array('msg' => 'pedido registrado', 'icono' => 'success');
            } else {
                $mensaje = array('msg' => 'error al registrar el pedido', 'icono' => 'error');
            }
        } else {
            $mensaje = array('msg' => 'error fatal con los datos', 'icono' => 'error');
        }
        echo json_encode($mensaje);
        die();
    }
    //listar productos pendientes del cliente
    public function listarPendientes()
    {
        $id_cliente = $_SESSION['idCliente'];
        $data = $this->model->getPedidos($id_cliente);
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['accion'] = '<div class="text-center"><button class="btn btn-primary" type="button" onclick="verPedido(' . $data[$i]['id'] . ')"><i class="fas fa-eye"></i></button></div>';
        }
        echo json_encode($data);
        die();
    }

    //ver los detalles de un pedido específico del cliente
    public function verPedido($idPedido)
    {
        $data['pedido'] = $this->model->getPedido($idPedido);
        $data['productos'] = $this->model->verPedidos($idPedido);
        $data['moneda'] = MONEDA;
        echo json_encode($data);
        die();
    }

    //cerrar la sesión del cliente
    public function salir()
    {
        session_destroy();
        header('Location: ' . BASE_URL);
    }
}
