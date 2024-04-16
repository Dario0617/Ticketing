<?php

namespace ticketing\controller;

use ticketing\model\Comment;
use ticketing\model\CommentManager;
use ticketing\model\Ticket;
use ticketing\model\TicketManager;
use ticketing\model\User;

class CommentController extends Controller
{
    private $CommentManager;
    private $TicketManager;

    public function __construct( array $params=[] )
    {
        $this->CommentManager = new CommentManager();
        $this->TicketManager = new TicketManager();
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
     *  saveComment
     */
    public function saveAction()
    {
        date_default_timezone_set('Europe/Paris');
        $this->checkConnexion();
        $data['alert'] = 'alert-danger';
        $data['message'] = "Erreur : Tous les champs obligatoires ne sont pas remplis";
        if(isset( $this->vars['message'] ) && $this->vars['message'] != ""){
            $message = $this->vars["message"];
            $ticket = new Ticket(['id'=>$this->vars["ticketId"]]);
            $user = new User(['id'=>$_SESSION['user']->GetId()]);
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
                            move_uploaded_file($_FILES["file"]["tmp_name"], "file/" . 
                            basename($_FILES["file"]["name"]));
                            $nameFile = $_FILES["file"]["name"];
                        }
                        else
                        {
                            $data['alert'] = 'alert-warning';
                            $data['message'] = "L'extension du fichier doit être jpg/jpeg/gif/png";
                            return $this->redirectToRoute('ticket/edit/id/'.$this->vars['ticketId'], $data);
                        }
                    }
                    else
                    {
                        $data['alert'] = 'alert-warning';
                        $data['message'] = "Votre fichier doit être inférieur a 1Mo";
                        return $this->redirectToRoute('ticket/edit/id/'.$this->vars['ticketId'], $data);
                    }
                } 
                else
                {
                    $data['alert'] = 'alert-danger';
                    $data['message'] = "Erreur : Lors de l'envoie du fichier";
                    return $this->redirectToRoute('ticket/edit/id/'.$this->vars['ticketId'], $data);
                }
            }
            $comment = new Comment(['message'=>$message, 'file'=>$nameFile, 'ticket'=>$ticket, 'user'=>$user,
            'creationDate'=>date("Y-m-d H:i:s")]);
            $this->CommentManager->CreateComment($comment);
            $ticket = $this->TicketManager->GetTicketById($ticket->GetId());
            $this->TicketManager->UpdateTicket($ticket);
            $data['alert'] = 'alert-success';
            $data['message'] = "Le commentaire a été créé avec succès";
        }
        return $this->redirectToRoute('ticket/edit/id/'.$this->vars['ticketId'], $data);
    }
}