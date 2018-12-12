<?php

namespace Library\Model;

final class Author
{
    /**
     * @var int - id of the author
     */
    private $id;

    /**
     * @var string - name of the author
     */
    private $name;


    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * getId returns the author id.
     *
     * @return int - the author id
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * getName returns the author name.
     *
     * @return string - the author name
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * toArray converts the properties of this class to an array.
     *
     * @return array - the class properties as an array
     */
    public function toArray()
    {
        $a = [];

        $a['id'] = $this->id;
        $a['name'] = $this->name;

        return $a;
    }
}
