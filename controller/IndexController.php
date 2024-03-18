<?php

namespace ticketing\controller;

class IndexController extends Controller
{

    public function __construct( array $params=[] )
    {
        parent::__construct( $params );
    }


    /**
     *  Default action, called if no action is detected
     */
    public function defaultAction()
    {
        $data = [
            "message"   => "bienvenue sur mon projet de ticketing"
        ];
        $this->render( 'index', $data );
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






