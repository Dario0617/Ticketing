<?php

namespace ticketing\model;

class CommentManager extends Manager
{
    public function CreateComment(Comment $comment)
    {
        date_default_timezone_set('Europe/Paris');
        $sql = 'INSERT INTO Comment (TicketId, Message, File, UserId, CreationDate) 
        VALUES (:ticketId, :message, :file, :userId, :creationDate)';
        $reponse = $this->manager->db->prepare( $sql );
        $reponse->execute(array(':ticketId'=>$comment->GetTicket()->GetId(), ':message'=>$comment->GetMessage(), 
        ':file'=>$comment->GetFile(), ':userId'=>$comment->GetUser()->GetId(), ':creationDate'=>date("Y-m-d H:i:s")));
        $comment->SetId($this->manager->db->lastInsertId());
        return $comment;
    }

    public function GetComments(int $ticketId)
    {
        $sql = "SELECT Comment.*, User.Login FROM Comment 
        INNER JOIN User ON Comment.UserId = User.Id WHERE TicketId = :ticketId";
        $reponse = $this->manager->db->prepare( $sql );
        $reponse->execute(array(':ticketId'=>$ticketId));
        $dataList = $reponse->fetchAll(\PDO::FETCH_ASSOC);
        $comments = [];
		foreach ( $dataList as $data ) {
            $user = new User (['login'=>$data['Login']]);
            $ticket = new Ticket(['id'=>$data['TicketId']]);
            $data['User'] = $user;
            $data['Ticket'] = $ticket;
			$comments[] = new Comment( $data );
		}
        return $comments;
    }
}