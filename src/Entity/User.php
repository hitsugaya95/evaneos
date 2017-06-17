<?php

class User
{
    public $id;
    public $firstname;
    public $lastname;
    public $email;

    /**
     * Mapping placeholders to a specific property
     */
    public static $placeholders = array(
        'first_name' => 'firstname',
        'last_name' => 'lastname',
        'email' => 'email'
    );

    public function __construct($id, $firstname, $lastname, $email)
    {
        $this->id = $id;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
    }
}
