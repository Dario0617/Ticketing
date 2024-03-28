<?php

namespace ticketing\controller;

use ticketing\model\PriorityManager;
use ticketing\model\RequestTypeManager;
use ticketing\model\Ticket;
use ticketing\model\TicketManager;

class TicketController extends Controller
{
    private $TicketManager;
    private $RequestType;
    private $Priority;

    public function __construct( array $params=[] )
    {
        $this->TicketManager = new TicketManager();
        $this->RequestType = new RequestTypeManager();
        $this->Priority = new PriorityManager();
        parent::__construct( $params );
    }

    /**
     *  Default action, load ticketsView
     */
    public function defaultAction()
    {
        $this->checkConnexion();
        $data = [];
        $this->render( 'tickets', $data );
    }

    /**
     *  load createTicketView
     */
    public function createAction()
    {
        $this->checkConnexion();
        $data = [
            'requestTypes' => $this->RequestType->GetRequestTypes(),
            'priorities' => $this->Priority->GetPriorities()
        ];
        $this->render("createTicket", $data);
    }

    /**
     *  saveTicket et redirect to ticketsView
     */
    public function saveAction()
    {
        $this->checkConnexion();
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
        $this->createAction();
    }
}