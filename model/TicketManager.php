<?php

namespace ticketing\model;

class TicketManager extends Manager
{
    public function GetTicketById($ticketId)
    {
        $sql = 'SELECT Ticket.*, RequestType.Name AS Type, Priority.Name AS Priority FROM Ticket 
            INNER JOIN RequestType ON Ticket.RequestTypeId = RequestType.Id 
            INNER JOIN Priority ON Ticket.PriorityId = Priority.Id WHERE Id=:id';
        $reponse = $this->manager->db->prepare( $sql );
        $reponse->execute(array(':id'=>$ticketId));
        return new User($reponse->fetch(\PDO::FETCH_ASSOC));
    }

    public function GetTickets(array $params)
    {
        $order = !empty( $params['order'] ) ? $params['order'] : 'ASC';
        $sort = !empty( $params['sort'] ) ? $params['sort'] : 'id';
        $limit = !empty( $params['limit'] ) ? $params['limit'] : 10;
        $offset = !empty( $params['offset'] ) ? $params['offset'] : 0;
        $strLike = false;
        if( !empty( $params['search'] ) && !empty( $params['searchable'] ) ) {
            foreach( $params['searchable'] as $searchItem ) {
                $search = $params['search'];
                $strLike .= $searchItem . " LIKE '%$search%' OR ";
            }
            $strLike = trim( $strLike, ' OR ' );
        }
        $sql = "SELECT Ticket.*, RequestType.Name AS Type, Priority.Name AS Priority FROM Ticket 
            INNER JOIN RequestType ON Ticket.RequestTypeId = RequestType.Id 
            INNER JOIN Priority ON Ticket.PriorityId = Priority.Id";
        if( $strLike ) {
            $sql .= " WHERE $strLike";
        }
        $sql .= " ORDER BY $sort $order";
        $sql .= " LIMIT $offset, $limit";
        $response = $this->manager->db->query( $sql );
		$dataList = $response->fetchAll( \PDO::FETCH_ASSOC );
        $tickets = [];
		foreach ( $dataList as $data ) {
			$tickets[] = new Ticket( $data );
		}
        return $tickets;
    }

    public function CreateTicket(Ticket $ticket)
    {
        $sql = 'INSERT INTO Ticket (RequestTypeId, PriorityId, Subject, Message, File, CreationDate, LastModificationDate)
         VALUES (:requestTypeId, :priorityId, :subject, :message, :file, :creationDate, :lastModificationDate)';
        $reponse = $this->manager->db->prepare( $sql );
        $reponse->execute(array(':requestTypeId'=>$ticket->GetType(), ':priorityId'=>$ticket->GetPriority(), ':subject'=>$ticket->GetSubject(), 
        ':message'=>$ticket->GetMessage(), ':file'=>$ticket->GetFile(), ':creationDate'=>$ticket->GetCreationDate(), ':lastModificationDate'=>$ticket->GetLastModificationDate()));
        $ticket->SetId($this->manager->db->lastInsertId());
        return $ticket;
    }

    /**
     * Count tickets
     *
     * @return integer
     */
    public function countAll()
    {
        $sql = "SELECT count(*) FROM Ticket";
        $response = $this->manager->db->query( $sql );
        $nbTickets = $response->fetch();
        return $nbTickets[0];
    }
}