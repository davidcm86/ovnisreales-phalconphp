<?php
namespace OvnisReales\Controllers\Admin;

use Phalcon\Http\Response;

use OvnisReales\Models\Usuarios;

class UsuariosController extends ControllerBase
{
    public function loginAction()
    {
        // si ya tiene login, que no entre aquí
        $usuario = $this->session->get('UsuarioAdmin');
        if (!$usuario) {
            if ($this->request->isPost()) {
                $result = $this->AuthPlugin->loginAdmin($this->request->getPost());
                if (empty($result['errores'])) {
                    return $this->response->redirect('/admin/videosFacebook');
                }
                $this->view->result = $result;
            }
        } else {
            return $this->response->redirect('/admin/videosFacebook');
        }
    }

    public function logoutAdminAction()
    {
        $this->view->disable();
        $this->AuthPlugin->logoutAdmin();
    }
}
