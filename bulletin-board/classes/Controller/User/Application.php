<?php

class Controller_User_Application extends Controller_Application
{
    protected $loginUser = null;

    public function setUp()
    {
        parent::setUp();

        $this->loginUser = $this->getSession('login_user');
    }
}
