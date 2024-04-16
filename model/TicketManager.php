<?php

namespace ticketing\model;

class TicketManager extends Manager
{
    public function GetTicketById($ticketId)
    {
        $sql = 'SELECT Ticket.*, RequestType.Name AS Type, Priority.Name AS Priority FROM Ticket 
            INNER JOIN RequestType ON Ticket.RequestTypeId = RequestType.Id 
            INNER JOIN Priority ON Ticket.PriorityId = Priority.Id WHERE Ticket.Id=:id';
        $reponse = $this->manager->db->prepare( $sql );
        $reponse->execute(array(':id'=>$ticketId));
        return new Ticket($reponse->fetch(\PDO::FETCH_ASSOC));
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
                if ($searchItem == "type"){
                    $searchItem = "RequestType.Name";
                }
                if ($searchItem == "priority"){
                    $searchItem = "Priority.Name";
                }
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
        if ($params['onlyClosed']){
            $sqlClosed = "Ticket.Closed = 1";
            if( $strLike ) {
                $sql .= " AND " . $sqlClosed;
            }else{
                $sql .= " WHERE " . $sqlClosed;
            }
        } elseif ($params['onlyOpened']){
            $sqlClosed = "Ticket.Closed = 0";
            if( $strLike ) {
                $sql .= " AND " . $sqlClosed;
            }else{
                $sql .= " WHERE " . $sqlClosed;
            }
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

    /**
     * Create ticket
     *
     * @return Ticket
     */
    public function CreateTicket(Ticket $ticket)
    {
        date_default_timezone_set('Europe/Paris');
        $sql = 'INSERT INTO Ticket (RequestTypeId, PriorityId, Subject, Message, File, CreationDate, LastModificationDate)
         VALUES (:requestTypeId, :priorityId, :subject, :message, :file, :creationDate, :lastModificationDate)';
        $reponse = $this->manager->db->prepare( $sql );
        $reponse->execute(array(':requestTypeId'=>$ticket->GetType(), ':priorityId'=>$ticket->GetPriority(), 
        ':subject'=>$ticket->GetSubject(), ':message'=>$ticket->GetMessage(), ':file'=>$ticket->GetFile(), 
        ':creationDate'=>date("Y-m-d H:i:s"), ':lastModificationDate'=>date("Y-m-d H:i:s")));
        $ticket->SetId($this->manager->db->lastInsertId());
        return $ticket;
    }

    /**
     * Count tickets
     *
     * @return integer
     */
    public function countAll(array $params)
    {
        $sql = "SELECT count(*) FROM Ticket";
        if ($params['onlyClosed']){
            $sql .= " WHERE Ticket.Closed = 1";
        } elseif ($params['onlyOpened']){
            $sql .= " WHERE Ticket.Closed = 0";
        }
        $response = $this->manager->db->query( $sql );
        $nbTickets = $response->fetch();
        return $nbTickets[0];
    }

    /**
     * Update ticket
     */
    public function UpdateTicket(Ticket $ticket)
    {
        date_default_timezone_set('Europe/Paris');
        $sql = 'UPDATE Ticket SET LastModificationDate = :lastModificationDate, Closed = :closed WHERE Id = :id';
        $reponse = $this->manager->db->prepare( $sql );
        return $reponse->execute(array(':lastModificationDate'=>date("Y-m-d H:i:s"), ':closed'=>$ticket->GetClosed(), 
        ':id'=>$ticket->GetId()));
    }
}