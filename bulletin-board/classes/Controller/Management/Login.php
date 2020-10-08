<?php

class Controller_Management_Login extends Controller_Management_Application
{
    public function login()
    {
        if (!empty($this->loginManager)) {
            $this->redirect('management/bulletin/index.php');
        }

        $errors = [];

        if ($this->getParam('do_login') === '1') {
            $loginId = $this->getParam('login_id');
            $pass    = $this->getParam('pass');

            if (is_empty($loginId)) {
                $errors[] = 'ID is empty.';
            }

            if (is_empty($pass)) {
                $errors[] = 'Password is empty.';
            }

            if (is_empty($errors)) {
                $manager = new Storage_Manager();
                $results = $manager->fetch(null, 'login_id = :login_id', ['login_id' => $loginId]);

                if (isset($results[0]) && $manager->verifyPassword($pass, $results[0]['pass'])) {
                    $_SESSION['login_manager'] = $results[0];
                    $this->redirect('management/bulletin/index.php');
                } else {
                    $errors[] = 'ID or Password is not corrected.';
                }
            }
        }

        $this->render('management/login.php', get_defined_vars());
    }

    public function logout()
    {
        if (empty($this->loginManager)) {
            $this->redirect('management/login.php');
        }

        $this->_logout('login_manager');

        $this->redirect('management/bulletin/index.php');
    }
}
