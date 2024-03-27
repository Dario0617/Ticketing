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

    public function LoginExist($login)
    {
        $sql = 'SELECT * FROM User WHERE Login=:login';
        $reponse = $this->manager->db->prepare( $sql );
        $reponse->execute( [':login'=>$login] );
        return $reponse->rowCount();
    }

    public function CreateUser(User $user)
    {
        $sql = 'INSERT INTO User (Login, Password) VALUES (:login,:password)';
        $reponse = $this->manager->db->prepare( $sql );
        $reponse->execute(array(':login'=>$user->GetLogin(), ':password'=>$user->GetPassword()));
        $user->SetId($this->manager->db->lastInsertId());
        return $user;
    }
}