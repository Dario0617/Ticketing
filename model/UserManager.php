<?php

namespace ticketing\model;

class UserManager extends Manager
{
    public function GetUserById($userId)
    {
        $sql = 'SELECT * FROM User WHERE Id=:id';
        $reponse = $this->manager->db->prepare( $sql );
        $reponse->execute(array(':id'=>$userId));
        return new User($reponse->fetch(\PDO::FETCH_ASSOC));
    }

    public function GetUserByLogin(User $user)
    {
        $sql = 'SELECT * FROM User WHERE Login=:login';
        $reponse = $this->manager->db->prepare($sql);
        $reponse->execute([':login'=>$user->GetLogin()]);
        if ($valeurs = $reponse->fetch(\PDO::FETCH_ASSOC)){
            $user = new User($valeurs);
            return $user;
        }
        return false;
    }
}