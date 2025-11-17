<?php
// app/core/Controller.php

class Controller
{
    protected function view($view, $data = [])
    {
        extract($data);
        require_once '../app/views/layout/header.php';
        require_once "../app/views/$view.php";
        require_once '../app/views/layout/footer.php';
    }
}
