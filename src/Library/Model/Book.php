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

    public function getAuthor() : Author
    {
        return $this->author;
    }

    public function setAuthor(Author $author)
    {
        $this->author = $author;
    }

    public function toArray() {
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
