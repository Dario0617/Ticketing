<?php

namespace ticketing\model;
use ticketing\model\RequestType;

class PriorityManager extends Manager
{
    public function GetPriorities()
    {
        $sql = 'SELECT * FROM Priority';
        $reponse = $this->manager->db->query( $sql );
        if ($reponse){
            $priorities = [];
            foreach($reponse->fetchAll(\PDO::FETCH_ASSOC) as $priority){
                $priorities[] = new RequestType($priority);
            }
            return $priorities;
        } else {
            return null;
        }
    }
}