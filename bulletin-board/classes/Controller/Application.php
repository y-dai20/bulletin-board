<?php

class Controller_Application extends Controller_Base
{
    protected $imageDir = '';

    protected function _logout($session_key)
    {
        unset($_SESSION[$session_key]);

        if (empty($_SESSION)) {
            setcookie(session_name(), '', time() - 1, '/');
            session_destroy();
        }
    }

    protected function createImageUploader()
    {
        return new Uploader_Image($this->imageDir);
    }
}
