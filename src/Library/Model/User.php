<?php

namespace Library\Model;

final class User
{
    /**
     * @var int - id of the user
     */
    private $id;

    /**
     * @var string - name of the user
     */
    private $name;

    /**
     * @var email - email of the user
     */
    private $email;


    public function __construct(int $id, string $name, string $email)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
    }

    /**
     * getId returns the user id.
     *
     * @return int - the user id
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * getName returns the user name.
     *
     * @return string - the user name
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * getEmail returns the user email.
     *
     * @return string - the user email
     */
    public function getEmail() : string
    {
        return $this->email;
    }
}