<?php

class Controller_Management_Application extends Controller_Application
{
    protected $loginManager = null;

    public function setUp()
    {
        parent::setUp();

        $this->loginManager = $this->getSession('login_manager');
    }
}
