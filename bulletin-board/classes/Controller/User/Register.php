<?php

class Controller_User_Register extends Controller_User_Application
{
    public function setUp()
    {
        parent::setUp();

        if (!empty($this->loginUser)) {
            $this->redirect('user/bulletin/index.php');
        }
    }

    public function register()
    {
        $name  = $this->getParam('name');
        $email = $this->getParam('email');
        $pass  = $this->getParam('pass');

        $data = [
            'name'  => $name,
            'email' => $email,
            'pass'  => $pass,
        ];

        $doConfirm  = $this->getParam('do_confirm');
        $doRegister = $this->getParam('do_register');

        if (empty($doConfirm) && empty($doRegister)) {
            $this->render('user/register.php', get_defined_vars());
            return;
        }

        $tempUser = new Storage_TempUser();
        $errors   = $tempUser->validate($data);

        $user    = new Storage_User();
        $results = $user->fetch(null, 'email = :email', ['email' => $email]);

        if (isset($results[0])) {
            $errors[] = 'The E-mail is already in use.';
            $this->render('user/register.php', get_defined_vars());
            return;
        }

        if (!empty($errors) || empty($doRegister)) {
            $this->render('user/register.php', get_defined_vars());
            return;
        }

        $token         = hash('sha256', uniqid(mt_rand(), true));
        $data['token'] = $token;
        $url           = "http://" . $this->envs['server-name'] . BASE_URI_PATH . "/certify.php?token={$token}";

        $tempUser->insert($data);

        $this->sendEmail($name, $email, $url);
    }

    protected function sendEmail($name, $email, $url)
    {
        $subject = 'Thank you for your membership register';
        $headers = "From: from@example.com";

        $body    = <<< EOM
        Dear {$name}

        To enable your account, please click in the
        following link or copy it onto the address bar of
        your favorite browser.

        {$url}

        Please click within 24 hours.
EOM;

        // study: ガード節（早期リターン）というものをちょっと勉強するといいかな
        if (mb_send_mail($email, $subject, $body, $headers)) {
            $this->render('user/sendEmail.php');
            return;
        } else {
            throw new Exception(__METHOD__ . "() couldn't send email.");
        }
    }

    public function certify()
    {
        $token = $this->getParam('token');

        if (empty($token)) {
            $this->err404();
        }

        $tempUser = new Storage_TempUser();
        $results  = $tempUser->fetch(null, 'token = :token', ['token' => $token]);

        if (!isset($results[0])) {
            $this->err404();
        }

        $user     = new Storage_User();
        $_results = $user->fetch(null, 'email = :email', ['email' => $results[0]['email']]);

        if (isset($_results[0])) {
            $errors = 'Your E-mail is already registered.';
            $this->render('user/certify.php', get_defined_vars());
            return;
        }

        $date     = new DateTime($results[0]['created_at']);
        $deadline = $date->modify('+1 day');
        $now      = new DateTime();

        // study: なんなら if ($now <= $deadline) でいいよねと言いたい所だけど、
        //      変数化することで目的がなんなのかが分かるようになるのでこのやり方はよいやり方だと思います。
        $overDeadline = ($now > $deadline);

        if ($overDeadline) {
            $errors = 'Registration deadline has passed.<br />Please register again.';
            $this->render('user/certify.php', get_defined_vars());
            return;
        }

        $name  = $results[0]['name'];
        $email = $results[0]['email'];
        $pass  = $results[0]['pass'];

        $data = [
            'name'  => $name,
            'email' => $email,
            'pass'  => $pass,
        ];

        $user = new Storage_User();
        $user->insert($data);

        $tempUser->deleteById($results[0]['id']);

        $loginUser              = $user->fetch(null, 'email = :email', ['email' => $email]);
        $_SESSION['login_user'] = $loginUser[0];

        $this->render('user/certify.php', get_defined_vars());
    }
}
