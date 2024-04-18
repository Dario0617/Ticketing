<?php

namespace ticketing\controller;

use ticketing\model\PriorityManager;
use ticketing\model\RequestTypeManager;
use ticketing\model\Ticket;
use ticketing\model\TicketManager;
use ticketing\model\CommentManager;

class TicketController extends Controller
{
    private $TicketManager;
    private $RequestTypeManager;
    private $PriorityManager;
    private $CommentManager;

    public function __construct( array $params=[] )
    {
        $this->TicketManager = new TicketManager();
        $this->RequestTypeManager = new RequestTypeManager();
        $this->PriorityManager = new PriorityManager();
        $this->CommentManager = new CommentManager();
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
    public function createAction(array $data=[])
    {
        $this->checkConnexion();
        $data['requestTypes'] = $this->RequestTypeManager->GetRequestTypes();
        $data['priorities'] = $this->PriorityManager->GetPriorities();
        $this->render("createTicket", $data);
    }

    /**
     *  saveTicket et redirect to ticketsView
     */
    public function saveAction()
    {
        date_default_timezone_set('Europe/Paris');
        $this->checkConnexion();
        $data['alert'] = 'alert-danger';
        $data['message'] = "Erreur : Tous les champs obligatoires ne sont pas remplis";
        if( isset( $this->vars['type'] ) && $this->vars['type'] != "" 
        && isset( $this->vars['priority'] ) && $this->vars['priority'] != ""
        && isset( $this->vars['subject'] ) && $this->vars['subject'] != "" 
        && isset( $this->vars['message'] ) && $this->vars['message'] != "" ){
            $type = $this->vars["type"];
            $priority = $this->vars["priority"];
            $subject = $this->vars["subject"];
            $message = $this->vars["message"];
            $nameFile = "";
            if (isset($_FILES["file"]) && $_FILES["file"]["name"] != "")
            {
                if ($_FILES["file"]["error"] == 0)
                {
                    if ($_FILES["file"]["size"] <= 1000000)
                    {
                        $infosFichier = pathinfo($_FILES["file"]["name"]);
                        $extensionUpload = $infosFichier["extension"];
                        $extensionsAutorisees = array("jpg", "jpeg", "gif", "png");
                        if (in_array($extensionUpload, $extensionsAutorisees))
                        {
                            move_uploaded_file($_FILES["file"]["tmp_name"], "file/" . basename($_FILES["file"]["name"]));
                            $nameFile = $_FILES["file"]["name"];
                        }
                        else
                        {
                            $data['alert'] = 'alert-warning';
                            $data['message'] = "L'extension du fichier doit être jpg/jpeg/gif/png";
                            return $this->createAction($data);
                        }
                    }
                    else
                    {
                        $data['alert'] = 'alert-warning';
                        $data['message'] = "Votre fichier doit être inférieur a 1Mo";
                        return $this->createAction($data);
                    }
                } 
                else
                {
                    $data['alert'] = 'alert-danger';
                    $data['message'] = "Erreur : Lors de l'envoie du fichier";
                    return $this->createAction($data);
                }
            }            
            $ticket = new Ticket(['type'=>$type, 'priority'=>$priority, 'subject'=>$subject, 'message'=>$message, 
            'file'=>$nameFile, 'creationDate'=>date("Y-m-d H:i:s"), 'lastModificationDate'=>date("Y-m-d H:i:s"), 
            'userId'=>$_SESSION['user']->GetId()]);
            $this->TicketManager->CreateTicket($ticket);
            $data['alert'] = 'alert-success';
            $data['message'] = "Le ticket a été créé avec succès";
            return $this->createAction($data);
        }
        $data['typeSelected'] = $this->vars['type'];
        $data['prioritySelected'] = $this->vars['priority'];
        $data['subject'] = $this->vars['subject'];
        $data['messageInput'] = $this->vars['message'];
        return $this->createAction($data);
    }

    /**
     *  Update Ticket
     */
    public function updateAction()
    {
        $this->checkConnexion();
        if( isset( $this->vars['id'] ) && $this->vars['id'] != ""
        && isset( $this->vars['close'] ) && $this->vars['close'] != ""){
            $ticket = new Ticket(['closed'=>$this->vars['close'], 'id'=>$this->vars['id']]);
            $this->TicketManager->UpdateTicket($ticket);
            $data['alert'] = 'alert-success';
            if ($this->vars['close']){
                $data['message'] = "Le ticket a été clôturé avec succès";
            } else{
                $data['message'] = "Le ticket a été réouvert avec succès";
            }
        }
        return $this->redirectToRoute('ticket/edit/id/'.$this->vars['id'], $data);
    }

    /**
     *  retrieve tickets
     */
    public function ticketsAction()
    {
        $searchParams = [
            'search'		=> $this->vars['search'],
			'sort'			=> $this->vars['sort'],
			'order'			=> $this->vars['order'],
			'offset'		=> $this->vars['offset'],
			'limit'			=> $this->vars['limit'],
			'searchable'	=> $this->vars['searchable'],
            'onlyClosed'    => $this->vars['isclose'] == 1,
            'onlyOpened'     => $this->vars['isnotclose'] == 1
        ];
        
        // $nbTickets = $this->TicketManager->CountAll($searchParams) ?? 0;
        $tickets = $this->TicketManager->GetTickets($searchParams );
        $searchParams['offset'] = "";
        $searchParams['limit'] = "";
        $nbTickets = count($this->TicketManager->GetTickets($searchParams ));

        $dataBs = [];
        foreach( $tickets as $ticket ) {
            $dataBs[] = [
                'id'                    => $ticket->GetId(),
                'subject'               => $ticket->GetSubject(),
                'creationDate'          => $ticket->GetCreationDate(),
                'lastModificationDate'  => $ticket->GetLastModificationDate(),
                'type'                  => $ticket->GetType(),
                'priority'              => $ticket->GetPriority(),
                'closed'                => $ticket->GetClosed() ? "Oui" : "Non"
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
        $this->checkConnexion();
        if (isset( $this->vars['alert'] ) && isset( $this->vars['message'] )){
            $data['alert'] = $this->vars['alert'];
            $data['message'] = $this->vars['message'];
        }
        if( isset( $this->vars['id'] ) ) {
            $ticket = $this->TicketManager->GetTicketById( $this->vars['id'] );
            if (isset($ticket)){
                $comments = $this->CommentManager->GetComments($ticket->GetId());
                $data['ticket'] = $ticket;
                $data['comments'] = $comments;
                return $this->render('editTicket', $data);
            }
            $data['alert'] = 'alert-danger';
            $data['message'] = "Erreur : Identifiant non reconnu";
        }
        $data['alert'] = 'alert-warning';
        $data['message'] = "Attention : Aucun identifiant trouvé";
        return $this->render( 'tickets', $data);
	}
}