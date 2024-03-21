<?php

namespace ticketing\controller;

use ticketing\model\Ticket;
use ticketing\model\TicketManager;

class TicketController extends Controller
{
    private $TicketManager;

    public function __construct( array $params=[] )
    {
        $this->TicketManager = new TicketManager();
        parent::__construct( $params );
    }

    /**
     *  Default action, load ticketsView
     */
    public function defaultAction()
    {
        $data = [];
        $this->render( 'tickets', $data );
    }

    /**
     *  load createTicketView
     */
    public function createAction()
    {
        $data = [];
        $this->render("createTicket", $data);
    }
}