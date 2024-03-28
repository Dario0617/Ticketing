<?php

namespace ticketing\model;
use ticketing\model\RequestType;

class RequestTypeManager extends Manager
{
    public function GetRequestTypes()
    {
        $sql = 'SELECT * FROM RequestType';
        $reponse = $this->manager->db->query( $sql );
        if ($reponse){
            $requestTypes = [];
            foreach($reponse->fetchAll(\PDO::FETCH_ASSOC) as $requestType){
                $requestTypes[] = new RequestType($requestType);
            }
            return $requestTypes;
        } else {
            return null;
        }
    }
}