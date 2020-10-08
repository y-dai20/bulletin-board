<?php

class Controller_User_Login extends Controller_User_Application
{
    public function login()
    {
        if (!empty($this->loginUser)) {
            $this->redirect('user/bulletin/index.php');
        }

        $errors = [];

        if ($this->getParam('do_login') === '1') {
            $email = $this->getParam('email');
            $pass  = $this->getParam('pass');

            if (is_empty($email)) {
                $errors[] = 'email is empty.';
            }

            if (is_empty($pass)) {
                $errors[] = 'password is empty.';
            }

            if (empty($errors)) {
                $user    = new Storage_User();
                $results = $user->fetch(null, 'email = :email', ['email' => $email]);

                // study: password_hash/verify関数は現実的な範囲で遅いのが理想な関数です
                //        それは、時間辺りの攻撃試行回数を減らす、解かれにくい難しい暗号化をするため
                //        じゃあこれを実行するかどうかでレスポンスタイムに大きな影響がある(可能性がある)
                //        そうなると、レスポンスタイムを見ることでメアドが登録されているかどうかが分かる(かもしれない)

                if (isset($results[0]) && $user->verifyPassword($pass, $results[0]['pass'])) {
                    $_SESSION['login_user'] = $results[0];
                    $this->redirect('user/bulletin/index.php');
                } else {
                    $errors[] = 'Email or Password is not corrected.';
                }
            }
        }

        $this->render('user/login.php', get_defined_vars());
    }

    public function logout()
    {
        if (empty($this->loginUser)) {
            $this->redirect('user/bulletin/index.php');
        }

        $this->_logout('login_user');

        $this->redirect('user/bulletin/index.php');
    }
}
