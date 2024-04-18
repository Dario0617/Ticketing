<?php

namespace ticketing\model;

use \DateTimeImmutable;
use \ReflectionMethod;

class Ticket {
    protected $Id;
    protected $Priority;
    protected $Type;
    protected $Subject;
    protected $Message;
    protected $File;
    protected $CreationDate;
    protected $LastModificationDate;
    protected $Closed;
    protected $UserId;

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

    public function GetPriority()
    {
        return $this->Priority;
    }
    public function SetPriority($priority)
    {
        return $this->Priority = $priority;
    }

    public function GetType()
    {
        return $this->Type;
    }
    public function SetType($type)
    {
        return $this->Type = $type;
    }

    public function GetSubject()
    {
        return $this->Subject;
    }
    public function SetSubject($subject)
    {
        return $this->Subject = $subject;
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

    public function GetCreationDate()
    {
        return $this->CreationDate->format('d/m/Y H:i:s');
    }
    public function SetCreationDate(?\DateTimeImmutable $creationDate)
    {
        return $this->CreationDate = $creationDate;
    }

    public function GetLastModificationDate()
    {
        return $this->LastModificationDate->format('d/m/Y H:i:s');
    }
    public function SetLastModificationDate(?\DateTimeImmutable $lastModificationDate)
    {
        return $this->LastModificationDate = $lastModificationDate;
    }

    public function GetClosed()
    {
        return $this->Closed;
    }
    public function SetClosed($closed)
    {
        return $this->Closed = $closed;
    }

    public function GetUserId()
    {
        return $this->UserId;
    }
    public function SetUserId($userId)
    {
        return $this->UserId = $userId;
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