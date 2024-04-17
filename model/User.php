<?php

namespace ticketing\model;

class User {
    private $Id;
    private $Login;
    private $Password;
    private $Admin;

    public function __construct(array $data){
        $this->Hydrate($data);
    }

    public function GetId()
    {
        return $this->Id;
    }
    public function SetId($id)
    {
        return $this->Id = $id;
    }

    public function GetLogin()
    {
        return $this->Login;
    }
    public function SetLogin($login)
    {
        return $this->Login = $login;
    }

    public function GetPassword()
    {
        return $this->Password;
    }
    public function SetPassword($password)
    {
        return $this->Password = $password;
    }

    public function GetAdmin()
    {
        return $this->Admin;
    }
    public function SetAdmin($admin)
    {
        return $this->Admin = $admin;
    }

    public function Hydrate(array $data){
        foreach( $data as $key => $value )
        {
            $method = 'Set'.ucfirst( $key );
            if (method_exists($this, $method))
            {
                $this->$method($value);
            }
        }
    }
}