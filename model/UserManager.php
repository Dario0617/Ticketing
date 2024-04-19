<?php

namespace ticketing\model;

class UserManager extends Manager
{
    /**
     * Get User by Id
     *
     * @return User
     */
    public function GetUserById($userId)
    {
        $sql = 'SELECT * FROM User WHERE Id=:id';
        $reponse = $this->manager->db->prepare( $sql );
        $reponse->execute(array(':id'=>$userId));
        return new User($reponse->fetch(\PDO::FETCH_ASSOC));
    }

    /**
     * Get User by login if exist
     *
     * @return User
     */
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

    /**
     * Check login Exist
     *
     * @return bool
     */
    public function LoginExist($login)
    {
        $sql = 'SELECT * FROM User WHERE Login=:login';
        $reponse = $this->manager->db->prepare( $sql );
        $reponse->execute( [':login'=>$login] );
        return $reponse->rowCount();
    }

    /**
     * Create User
     *
     * @return User
     */
    public function CreateUser(User $user)
    {
        $sql = 'INSERT INTO User (Login, Password, Admin) VALUES (:login,:password, 0)';
        $reponse = $this->manager->db->prepare( $sql );
        $reponse->execute(array(':login'=>$user->GetLogin(), ':password'=>$user->GetPassword()));
        $user->SetId($this->manager->db->lastInsertId());
        return $user;
    }

    /**
     * Count tickets
     *
     * @return integer
     */
    public function CountAll(int $id)
    {
        $sql = "SELECT count(*) FROM User WHERE Id <> :id";
        $response = $this->manager->db->prepare( $sql );
        $response->execute(array(':id'=>$id));  
        $nbUsers = $response->fetch();
        return $nbUsers[0];
    }

    /**
     * Get Users
     *
     * @return list User
     */
    public function GetUsers(array $params)
    {
        $order = !empty( $params['order'] ) ? $params['order'] : 'ASC';
        $sort = !empty( $params['sort'] ) ? $params['sort'] : 'id';
        $limit = !empty( $params['limit'] ) ? $params['limit'] : 10;
        $offset = !empty( $params['offset'] ) ? $params['offset'] : 0;
        $strLike = false;
        if( !empty( $params['search'] ) && !empty( $params['searchable'] ) ) {
            foreach( $params['searchable'] as $searchItem ) {
                if ($searchItem == "type"){
                    $searchItem = "RequestType.Name";
                }
                if ($searchItem == "priority"){
                    $searchItem = "Priority.Name";
                }
                $search = $params['search'];
                $strLike .= $searchItem . " LIKE '%$search%' OR ";
            }
            $strLike = trim( $strLike, ' OR ' );
        }
        $sql = "SELECT * FROM User";
        if( $strLike ) {
            $sql .= " WHERE $strLike AND Id <> :id";
        }
        if (!$strLike){
            $sql .= " WHERE Id <> :id ";
        }
        $sql .= " ORDER BY $sort $order";
        $sql .= " LIMIT $offset, $limit";
        $response = $this->manager->db->prepare( $sql );
        $response->execute(array(':id'=>$_SESSION['user']->GetId()));    
		$dataList = $response->fetchAll( \PDO::FETCH_ASSOC );
        $users = [];
		foreach ( $dataList as $data ) {
			$users[] = new User( $data );
		}
        return $users;
    }

    /**
     * Update User
     */
    public function UpdateUser(User $user)
    {
        $sql = 'UPDATE User SET Admin = :admin WHERE Id = :id';
        $response = $this->manager->db->prepare( $sql );
        return $response->execute(array(':admin'=>$user->GetAdmin(), ':id'=>$user->GetId()));
    }

    /**
     * Delete User
     */
    public function DeleteUser(int $userId)
    {
        $sql = 'DELETE FROM User WHERE Id = :id';
        $response = $this->manager->db->prepare( $sql );
        return $response->execute(array(':id'=>$userId));
    }
}