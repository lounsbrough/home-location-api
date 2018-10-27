<?php
class RequestParser
{
    public $location;
    public $person;
    public $status;

    public function parseRequest()
    {
        if (empty($this->requestBody['location']))
        {
            throw new Exception('Location was not specified');
        }
        
        $this->location = strtolower(trim($this->requestBody['location']));

        if (empty($this->requestBody['person']))
        {
            throw new Exception('Person was not specified');
        }
        
        $this->person = strtolower(trim($this->requestBody['person']));

        if (empty($this->requestBody['status']))
        {
            throw new Exception('Status was not specified');
        }
        
        $this->status = strtolower(trim($this->requestBody['status']));
    }
}
?>