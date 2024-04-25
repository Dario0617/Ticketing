<?php

namespace ticketing\model;

use ticketing\classes\dbConnect;

class Manager
{
    private $_dsn = 'mysql:host=localhost:3306;dbname=';
    private $_login;
    private $_password;
    protected $manager;

    public function __construct()
    {
        $envFilePath = dirname(__DIR__) . '/.env';
        if (file_exists($envFilePath)){
            $envData = file_get_contents($envFilePath, false);
            $tmp = explode(";", $envData);
            array_pop($tmp);
            $list = [];
            foreach($tmp as $elem){
                $e = explode('=', $elem);
                $list[$e[0]] = $e[1];                
            }
        } else {
            die();
        }
        if( strstr( $_SERVER['HTTP_HOST'], $list['DB_HOST'] ) ) {
			$this->_dsn .= 'dario_3';
            $this->_login = $list['DB_LOGIN'];
            $this->_password = $list['DB_PASSWORD'];
        } else {
            $this->_dsn .= 'ticketing';
            $this->_login = 'root';
            $this->_password = '';
        }
        $this->manager = dbConnect::getDb($this->_dsn, $this->_login, $this->_password );


    }

}