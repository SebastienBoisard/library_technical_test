<?php

namespace Library\Model;

use Library\Model\Author;
use Library\Model\Book;
use Library\Util\Logger;


class AuthorRepository
{
    /**
     * @var \mysqli - the connection to the databse
     */
    private $db_conn;

    /**
     * @var string - name of the book
     */
    private $name;


    public function __construct(\mysqli $db_conn) {
        $this->db_conn = $db_conn;
    }

    /**
     * getBook returns a book from its id.
     *
     * @param int $bookId  - the book id 
     * @return Book - the matching book
     * @throws Exception
     */
    public function getAuthorBooks(string $author_name) : ?Array {

        // Sanitize the author_name
        $author_name = $this->db_conn->real_escape_string($author_name);

        $query = 'SELECT books.id, books.name '.
            'FROM author JOIN books ON author.id = books.author '.
            "WHERE LOWER(author.name) = '".$author_name."'";

        $result = $this->db_conn->query($query);

        if ($result === false) {
            throw new \Exception("Database error with query `".$query."` (error=".$mysqli->error.")");
        }

        if ($result->num_rows == 0) {
            // No book found
            return null;
        } 

        $books = [];

        while ($row = $result->fetch_array()) {
            $books[] = new Book($row['id'], $row['name']);
        }

        $result->free();

        return $books;
    }
}
