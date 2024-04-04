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
            $file = $this->vars["file"];
            $ticket = new Ticket(['type'=>$type, 'priority'=>$priority, 'subject'=>$subject, 'message'=>$message, 
            'file'=>$file, 'creationDate'=>date("Y-m-d H:i:s"), 'lastModificationDate'=>date("Y-m-d H:i:s")]);
            $this->TicketManager->CreateTicket($ticket);
        }
        //$this->defaultAction();
    }

    /**
     *  retrieve tickets
     *
     * @return void
     */
    public function ticketsAction()
    {
        $nbTickets = $this->TicketManager->countAll() ?? 0;

        $searchParams = [
            'search'		=> $this->vars['search'],
			'sort'			=> $this->vars['sort'],
			'order'			=> $this->vars['order'],
			'offset'		=> $this->vars['offset'],
			'limit'			=> $this->vars['limit'],
			'searchable'	=> $this->vars['searchable']
        ];
        $tickets = $this->TicketManager->GetTickets( $searchParams );

        $dataBs = [];
        foreach( $tickets as $ticket ) {
            $dataBs[] = [
                'id'                    => $ticket->GetId(),
                'subject'               => $ticket->GetSubject(),
                'creationDate'          => $ticket->GetCreationDate()->format('d/m/Y H:i:s'),
                'lastModificationDate'  => $ticket->GetLastModificationDate()->format('d/m/Y H:i:s'),
                'type'                  => $ticket->GetType(),
                'priority'              => $ticket->GetPriority()
            ];
        }

        $data = [
            "rows"      => $dataBs,
            "total"     => $nbTickets
        ];
        $jsData = json_encode( $data );
        echo $jsData;
    }

    public function editAction()
	{
		$data = [
			'error' => false
		];
        if( isset( $this->vars['id'] ) ) {
            $ticket = $this->TicketManager->GetTicketById( $this->vars['id'] );
            if( isset( $this->vars['ValidProfilUpdate']) ) {
                $user->setName(  $this->vars['name'] );
                $user->setSurname( $this->vars['surname'] );

                $data['id'] = $user->getId();
                if( $this->usersManager->updateUser( $user ) ) {
                    $data['message'] = 'Utilidateur a été mise à jour avec succès.';
                    return $this->redirectToRoute( 'users/detailuser', $data );
                } else {
                    $data['error'] = true;
                    $data['message'] = 'Erreur lors de l\'enregistrement';
                }
            } else {
                return $this->render( 'users/profiluser', ['user'=>$user] );
            }
        }
        return $this->render( 'user/profiluser', [
            'status'    => 'warning',
            'message'   => 'Erreur ! Identitiant invalide'
        ]);

	}
}