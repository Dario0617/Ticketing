<?php

namespace ticketing\controller;

use ticketing\model\User;
use ticketing\model\UserManager;

class SecurityController extends Controller
{
    private $UserManager;

    public function __construct( array $params=[] )
    {
        $this->UserManager = new UserManager();
        parent::__construct( $params );
    }

    /**
     *  Default action, called if no action is detected
     */
    public function defaultAction()
    {
        $data = [];
        $this->render( 'connexion', $data );
    }

    /**
     *  Login user and acces to the app
     */
    public function loginAction()
    {
        $data = [];
        if( isset( $this->vars['login'] ) && isset( $this->vars['password'] )){
            $login = $this->vars["login"];
            $password = $this->vars["password"];
            $user = new User(['login'=>$login, 'password'=>$password]);
            $user = $this->UserManager->GetUserByLogin($user);
            if($user){
                $data['user'] = $user;
                if (sodium_crypto_pwhash_str_verify($user->GetPassword(), $user->GetPassword())){
                    new TicketController();
                    die;
                }
            }
            $data['alert'] = 'alert-danger';
            $data['message'] = "Erreur : Votre login / mot de passe est non valide !";
        }
        $this->render("connexion", $data);
    }

    /**
     *  Destroy session vars & redirect to home
     */
    public function logoutAction()
    {
        session_destroy();
        header('Location: .');
        exit();
    }

}






