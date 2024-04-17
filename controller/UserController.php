<?php

namespace ticketing\controller;

use ticketing\model\User;
use ticketing\model\UserManager;

class UserController extends Controller
{
    private $UserManager;

    public function __construct( array $params=[] )
    {
        $this->UserManager = new UserManager();
        parent::__construct( $params );
    }

    /**
     *  Default action, load userManagementView
     */
    public function defaultAction()
    {
        $this->checkConnexion();
        $data = [];
        if (isset( $this->vars['alert'] ) && isset( $this->vars['message'] )){
            $data['alert'] = $this->vars['alert'];
            $data['message'] = $this->vars['message'];
        }
        $this->render( 'userManagement', $data );
    }

    /**
     *  retrieve users
     */
    public function usersAction()
    {
        $searchParams = [
            'search'		=> $this->vars['search'],
			'sort'			=> $this->vars['sort'],
			'order'			=> $this->vars['order'],
			'offset'		=> $this->vars['offset'],
			'limit'			=> $this->vars['limit'],
			'searchable'	=> $this->vars['searchable']
        ];

        $nbTickets = $this->UserManager->CountAll() ?? 0;
        $users = $this->UserManager->GetUsers($searchParams);

        $dataBs = [];
        foreach( $users as $user ) {
            $dataBs[] = [
                'id'    => $user->GetId(),
                'admin' => $user->GetAdmin(),
                'login' => $user->GetLogin()
            ];
        }

        $data = [
            "rows"      => $dataBs,
            "total"     => $nbTickets
        ];
        $jsData = json_encode( $data );
        echo $jsData;
    }

    /**
     *  Update User
     */
    public function updateAction()
    {
        $this->checkConnexion();
        if( isset( $this->vars['id'] ) && $this->vars['id'] != ""
        && isset( $this->vars['admin'] ) && $this->vars['admin'] != ""){
            $admin = $this->vars['admin'];
            $user = new User(['id'=>$this->vars['id'], 'admin'=>$admin]);
            $this->UserManager->UpdateUser($user);
            $data['alert'] = 'alert-success';
            if ($admin){
                $data['message'] = "L'utilisateur est passé admin avec succès";
            } else{
                $data['message'] = "L'utilisateur est passé client avec succès";
            }
        }
        return $this->redirectToRoute('user', $data);
    }

    /**
     *  Delete User
     */
    public function deleteAction()
    {
        $this->checkConnexion();
        if( isset( $this->vars['id'] ) && $this->vars['id'] != ""){
            $this->UserManager->DeleteUser($this->vars['id']);
            $data['alert'] = 'alert-success';
            $data['message'] = "L'utilisateur a été supprimé avec succès";
        }
        return $this->redirectToRoute('user', $data);
    }
}