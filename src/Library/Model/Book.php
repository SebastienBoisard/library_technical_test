<?php

namespace Library\Model;

final class Book
{
    /**
     * @var int - id of the book
     */
    private $id;

    /**
     * @var string - name of the book
     */
    private $name;

    /**
     * @var string - type of the book
     */
    private $type;

    /**
     * @var Author - author of the book
     */
    private $author;


    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
        $this->type = 'book';
        $this->author = null;
    }

    /**
     * getId returns the book id.
     *
     * @return int - the book id
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * getName returns the book name.
     *
     * @return string - the book name
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * getAuthor returns the author object describing the author of the book.
     *
     * @return Author - the author object
     */
    public function getAuthor() : Author
    {
        return $this->author;
    }

    /**
     * setAuthor sets the author object.
     *
     * @param Author $author - the author object
     */
    public function setAuthor(Author $author)
    {
        $this->author = $author;
    }

    /**
     * toArray converts the properties of this class to an array.
     *
     * @return array - the class properties as an array
     */
    public function toArray() : array
    {
        $a = [];

        $a['id'] = $this->id;
        $a['type'] = $this->type;
        $a['title'] = $this->name;
        if ($this->author != null) {
            $a['author'] = $this->author->toArray();
        }

        return $a;
    }
}