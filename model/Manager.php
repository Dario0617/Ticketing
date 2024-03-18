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
        if( strstr( $_SERVER['HTTP_HOST'], '51.178.86.117' ) ) {
			$this->_dsn .= 'dario_3';
            $this->_login = 'dario';
            $this->_password = 'dab3oeP-';
        } else {
            $this->_dsn .= 'ticketing';
            $this->_login = 'root';
            $this->_password = '';
        }
        $this->manager = dbConnect::getDb($this->_dsn, $this->_login, $this->_password );


    }

}