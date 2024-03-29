<?php
namespace ticketing\classes;

use PDO;

class dbConnect
{
    public $db;
    private static $instance = null;

    public function __construct($dsn, $login, $password)
    {
        try {
            $this->db = new PDO($dsn, $login, $password);
            // An alert is issued each time a request fails.
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        } catch (\PDOException $e) {
            die('Echec de connexion, erreur n° ' . $e->getCode() . ':' . $e->getMessage() );
        }
    }


    public static function getDb($dsn,$login,$password)
    {
        if( is_null( self::$instance )) {
            self::$instance = new dbConnect($dsn, $login, $password);
        }
        return self::$instance;
    }
}