<?php

namespace ticketing\model;

class RequestType {
    private $Id;
    private $Name;

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

    public function GetName()
    {
        return $this->Name;
    }
    public function SetName($Name)
    {
        return $this->Name = $Name;
    }

    public function Hydrate(array $data){
        foreach( $data as $key => $value )
        {
            $method = 'Set'.ucfirst( $key );
            if (method_exists($this, $method))
            {
                $this->$method($value);
            }
        }
    }
}