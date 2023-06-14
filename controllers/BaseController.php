<?php

class BaseController
{
    public function getBeanById($typeOfBean, $queryStringKey)
    {

        $bean = R::load($typeOfBean, $queryStringKey);
        return $bean;
    }

    public function authorizeUser()
    {
        if (!isset($_SESSION["id"]) || !$_SESSION["id"]) {
                $controller = new UserController();
                $controller->login();


            exit;
        }
    }
}
