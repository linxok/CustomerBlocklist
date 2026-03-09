<?php
namespace MyCompany\CustomerBlocklist\Model;

class CheckoutContext
{
    private string $email;
    private string $telephone;
    private string $firstname;
    private string $lastname;

    public function __construct(string $email, string $telephone, string $firstname, string $lastname)
    {
        $this->email = $email;
        $this->telephone = $telephone;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getTelephone(): string
    {
        return $this->telephone;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }
}
