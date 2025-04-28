<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
class Admin extends Controller
{
    public function __construct()
    {
        parent::__construct();
        session_start();
    }

    //muestra la página de inicio de sesión si el administrador no está logueado
    public function index()
    {
        if (!empty($_SESSION['nombre_usuario'])) {
            header('Location: '. BASE_URL . 'admin/home');
            exit;
        }
        $data['title'] = 'Acceso al sistema';
        $this->views->getView('admin', "login", $data);
    }
    public function recovery() {
        // Puedes enviar datos a la vista si lo necesitas, por ejemplo, el título de la página
        $data['title'] = 'Recuperar Contraseña';
        $this->views->getView('admin', "recovery", $data);
    }
    public function sendRecovery() {
        if (isset($_POST['email']) && !empty($_POST['email'])) {
            $correo = $_POST['email'];
            $dataUser = $this->model->getUsuario($correo);
            if (!empty($dataUser)) {
                $token = md5(uniqid(rand(), true));
                $update = $this->model->updateToken($correo, $token);
                if ($update) {
                    $mail = new PHPMailer(true);
                    try {
                        $mail->SMTPDebug = 0;
                        $mail->isSMTP();
                        $mail->Host       = HOST_SMTP;        // Ej.: smtp.gmail.com
                        $mail->SMTPAuth   = true;
                        $mail->Username   = USER_SMTP;        // Tu usuario SMTP
                        $mail->Password   = PASS_SMTP;        // Tu contraseña SMTP
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                        $mail->Port       = PUERTO_SMTP;       // Ej.: 465

                        $mail->CharSet = 'UTF-8';
                        
                        $mail->setFrom('pijamas.shalom.notificaciones@gmail.com', TITLE);
                        $mail->addAddress($correo);

                        // Contenido del correo
                        $mail->isHTML(true);
                        $mail->Subject = 'Recuperación de Contraseña - ' . TITLE;
                        $mail->Body    = 'Para recuperar tu contraseña, haz clic en el siguiente enlace: <a href="' . BASE_URL . 'admin/resetPassword/' . $token . '">Recuperar Contraseña</a>';
                        $mail->AltBody = 'Para recuperar tu contraseña, visita: ' . BASE_URL . 'admin/resetPassword/' . $token;

                        $mail->send();
                        $mensaje = array('msg' => 'Correo enviado. Revisa tu bandeja de entrada.', 'icono' => 'success');
                    } catch (Exception $e) {
                        $mensaje = array('msg' => 'Error al enviar correo: ' . $mail->ErrorInfo, 'icono' => 'error');
                    }
                } else {
                    $mensaje = array('msg' => 'Error al actualizar el token.', 'icono' => 'error');
                }
            } else {
                $mensaje = array('msg' => 'El correo no existe.', 'icono' => 'error');
            }
        } else {
            $mensaje = array('msg' => 'El correo es requerido.', 'icono' => 'error');
        }
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        exit;
    }
//restablecer contraseña nueva
public function resetPassword($token) {
    $user = $this->model->getUserByToken($token);
    if (empty($user)) {
        header('Location: ' . BASE_URL . 'admin?msg=token_invalido');
        exit;
    }
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (empty($_POST['new_password']) || empty($_POST['confirm_password'])) {
            $data['error'] = 'Todos los campos son requeridos.';
            $data['title'] = 'Restablecer Contraseña';
            $data['token'] = $token;
            $this->views->getView('admin', 'reset_password', $data);
            return;
        }
        
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];
        
        if ($newPassword !== $confirmPassword) {
            $data['error'] = 'Las contraseñas no coinciden.';
            $data['title'] = 'Restablecer Contraseña';
            $data['token'] = $token;
            $this->views->getView('admin', 'reset_password', $data);
            return;
        }
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $update = $this->model->updateNewPassword($user['correo'], $hashedPassword);
        if ($update) {
            $this->model->clearToken($user['correo']);
            header('Location: ' . BASE_URL . 'admin?msg=password_updated');
            exit;
        } else {
            $data['error'] = 'Error al actualizar la contraseña. Inténtalo de nuevo.';
            $data['title'] = 'Restablecer Contraseña';
            $data['token'] = $token;
            $this->views->getView('admin', 'reset_password', $data);
            return;
        }
    } else {
        $data['title'] = 'Restablecer Contraseña';
        $data['token'] = $token;
        $this->views->getView('admin', 'reset_password', $data);
    }
}

    //validar las credenciales de inicio de sesión del administración
    public function validar()
    {
        if (isset($_POST['email']) && isset($_POST['clave'])) {
            if (empty($_POST['email']) || empty($_POST['clave'])) {
                $respuesta = array('msg' => 'todo los campos son requeridos', 'icono' => 'warning');
            } else {
                $data = $this->model->getUsuario($_POST['email']);
                if (empty($data)) {
                    $respuesta = array('msg' => 'el correo no existe', 'icono' => 'warning');
                } else {
                    if (password_verify($_POST['clave'], $data['clave'])) {
                        $_SESSION['email'] = $data['correo'];
                        $_SESSION['nombre_usuario'] = $data['nombres'];
                        $respuesta = array('msg' => 'datos correcto', 'icono' => 'success');
                    } else {
                        $respuesta = array('msg' => 'contraseña incorrecta', 'icono' => 'warning');
                    }
                }
            }
        } else {
            $respuesta = array('msg' => 'error desconocido', 'icono' => 'error');
        }
        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        die();
    }

    //muestra la página principal de administración si el está logueado, muestra las estadísticas
    public function home()
    {
        if (empty($_SESSION['nombre_usuario'])) {
            header('Location: '. BASE_URL . 'admin');
            exit;
        }
        $data['title'] = 'administracion';
        
        
        $this->views->getView('admin/administracion', "index", $data);
    }

    
    // Método para cerrar la sesión del usuario
    public function salir()
    {
        session_destroy();
        header('Location: ' . BASE_URL);
    }
}
