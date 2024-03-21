<?php

namespace ticketing\model;

class TicketManager extends Manager
{
    public function GetTicketById($ticketId)
    {
        $sql = 'SELECT * FROM Ticket WHERE Id=:id';
        $reponse = $this->manager->db->prepare( $sql );
        $reponse->execute(array(':id'=>$ticketId));
        return new User($reponse->fetch(\PDO::FETCH_ASSOC));
    }

    public function GetTickets()
    {
        $sql = 'SELECT * FROM Ticket';
        $reponse = $this->manager->db->prepare($sql);
        $reponse->execute();
        if ($valeurs = $reponse->fetch(\PDO::FETCH_ASSOC)){
            $user = new User($valeurs);
            return $user;
        }
        return false;
    }
}