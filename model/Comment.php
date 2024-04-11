<?php

namespace ticketing\model;

use \DateTimeImmutable;
use \ReflectionMethod;

class Comment {
    protected $Id;
    protected $Ticket;
    protected $Message;
    protected $File;
    protected $User;
    protected $CreationDate;

    public function __construct(array $data){
        $this->Hydrate($data);
    }

    public function GetId()
    {
        return $this->Id;
    }
    public function SetId($id)
    {
        return $this->Id = $id;
    }

    public function GetTicket()
    {
        return $this->Ticket;
    }
    public function SetTicket(Ticket $ticket)
    {
        return $this->Ticket = $ticket;
    }

    public function GetMessage()
    {
        return $this->Message;
    }
    public function SetMessage($message)
    {
        return $this->Message = $message;
    }

    public function GetFile()
    {
        return $this->File;
    }
    public function SetFile($file)
    {
        return $this->File = $file;
    }

    public function GetUser()
    {
        return $this->User;
    }
    public function SetUser(User $user)
    {
        return $this->User = $user;
    }

    public function GetCreationDate()
    {
        return $this->CreationDate->format('d/m/Y H:i:s');
    }
    public function SetCreationDate(?\DateTimeImmutable $creationDate)
    {
        return $this->CreationDate = $creationDate;
    }

    /**
     * Fill each property with the values present in $data
     *
     * @param array $data
     */
    public function Hydrate(array $data){
        foreach ( $data as $key=>$value ) {
            $method = 'set' . ucfirst($key);
            if( method_exists( $this, $method ) ) {
				$reflectionMethod = new ReflectionMethod($this, $method);
				$parameters = $reflectionMethod->getParameters();
				if (!empty($parameters)) {
					$parameterType = $parameters[0]->getType();
					if ($parameterType && $parameterType->getName() === 'DateTimeImmutable') {
						$value = new DateTimeImmutable($value);
					}
				}
                $this->$method($value);
            }
        }
    }
}