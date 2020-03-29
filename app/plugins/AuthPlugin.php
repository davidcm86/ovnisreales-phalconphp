<?php

use Phalcon\Mvc\User\Plugin;
use Phalcon\Http\Response;
use Phalcon\Security\Random;

use OvnisReales\Models\Usuarios;

use Phalcon\Validation;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\PresenceOf;

class AuthPlugin extends Plugin
{
	

    /**
     * Seteamos las sessiones del usuario para hacer login
     */
    private function __setSessionLoginUsuario($usuario, $nombreSession = 'Usuario') {

        $sessionUsuario = ['id' => $usuario->id, 'email' => $usuario->email, 'created' => time()];
        $this->flashSession->success('Bienvenida a Ovnis Reales');
        $this->session->set($nombreSession, $sessionUsuario);
        return;
    }

    public function hash($password) {
        return hash('sha512', SALT . $password);
    }

    private function __verify($passwordBd, $password) {
        if ($passwordBd == $this->hash($password)) return true;
        return false;
    }

    public function loginAdmin($data)
	{
        $return = array();
        $validation = new Validation();
        $validation->add('email',new PresenceOf(['message' => 'El email es requerido']));
        $validation->add('email', new Email(['message' => 'El email no es válido']));
        $messages = $validation->validate($_POST);
        if (count($messages)) {
            foreach ($messages as $message) {
                $return['errores'][] = $message->getMessage();
            }
        } else {
            $usuarios = new Usuarios();
            $parameters = ["email = '".$data['email']."'"];
            $usuario = $usuarios::findFirst($parameters);
            if (!empty($usuario)) {
                if ($this->__verify($usuario->password, $data['password'])) {
                    $this->__setSessionLoginUsuario($usuario, 'UsuarioAdmin');
                } else {
                    $return['errores'][] = 'Contraseña incorrecta.';
                }
            } else {
                $return['errores'][] = 'Email incorrecto.';
            }
        }
        return $return;
    }

    public function logoutAdmin()
	{
        $this->session->destroy();
        $this->response->redirect('/admin/usuarios/login');
    }
}
