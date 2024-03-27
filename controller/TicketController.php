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

    /**
     *  saveTicket et redirect to ticketsView
     */
    public function saveAction()
    {
        $data = [];
        if( isset( $this->vars['type'] ) && isset( $this->vars['priority'] ) && isset( $this->vars['subject'] ) && 
        isset( $this->vars['message'] )){
            $type = $this->vars["type"];
            $priority = $this->vars["priority"];
            $subject = $this->vars["subject"];
            $message = $this->vars["message"];
            //$file = $this->vars["file"];
            $ticket = new Ticket(['type'=>$type, 'priority'=>$priority, 'subject'=>$subject, 'message'=>$message]);
            //$this->TicketManager->SaveTicket($ticket);
        }
        $this->render("createTicket", $data);
    }
}