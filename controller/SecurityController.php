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
     *  load registerView
     */
    public function registerAction()
    {
        $data = [];
        $this->render( 'register', $data );
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
            $user2 = $this->UserManager->GetUserByLogin($user);
            if($user2){
                if (sodium_crypto_pwhash_str_verify($user2->GetPassword(), $user->GetPassword())){
                    $_SESSION['user'] = $user2;
                    new TicketController();
                    die;
                } else {
                    $data['alert'] = 'alert-danger';
                    $data['message'] = "Erreur : Votre mot de passe est non valide";
                } 
            } else {
                $data['alert'] = 'alert-danger';
                $data['message'] = "Erreur : Votre login est non valide ! </br> Avez-vous un compte ?";
            }
            $data['login'] = $user->GetLogin();
        }
        $this->render("connexion", $data);
    }

    /**
     *  Register user and acces to the app
     */
    public function createAccountAction()
    {
        $data = [];
        if( isset( $this->vars['login'] ) && isset( $this->vars['password'] ) && isset( $this->vars['confirmPassword'] )){
            $login = htmlentities($_POST['login'],ENT_COMPAT,"ISO-8859-1",true);
            $password = htmlentities($_POST['password'],ENT_COMPAT,"ISO-8859-1",true);
            $confirmPassword = htmlentities($_POST['confirmPassword'],ENT_COMPAT,"ISO-8859-1",true);
            $user = new User(['login'=>$login, 'password'=>$password]);
            if (!(strlen($password) >= 8)){
                $data['alert'] = 'alert-danger';
                $data['message'] = 'Erreur : Le mot de passe doit contenir au minimum 8 caractères !';
                $data['login'] = $user->GetLogin();
            } elseif ($password !== $confirmPassword){
                $data['alert'] = 'alert-danger';
                $data['message'] = 'Erreur : Les mots de passe doivent être identiques !';
                $data['login'] = $user->GetLogin();
            } else if ($this->UserManager->LoginExist($login)){
                $data['alert'] = 'alert-danger';
                $data['message'] = 'Erreur : Ce login existe déjà !';
                $data['login'] = $user->GetLogin();
            } else {
                $password = sodium_crypto_pwhash_str($password, SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE, 
                SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE);
                $user->SetPassword($password);
                $user = $this->UserManager->CreateUser($user);
                $_SESSION['user'] = $user;
                new TicketController();
                die;
            }
        }
        $this->render("register", $data);
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






