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


    public function __construct(\mysqli $db_conn)
    {
        $this->db_conn = $db_conn;
    }

    /**
     * getAuthorBooks returns all the books of an author.
     *
     * @param int $bookId - the book id 
     * @return array - the books of the author (or null if the author is not found or no book is found)
     * @throws Exception
     */
    public function getAuthorBooks(string $author_name, string $order) : ?Array
    {
        // Sanitize the author_name
        $author_name = $this->db_conn->real_escape_string($author_name);

        // Handle the order results (by id or by title, default is by title)
        if ($order == 'id') {
            $order_by = 'ORDER BY books.id';
        } else {
            $order_by = 'ORDER BY books.name';            
        }

        $query = 'SELECT books.id, books.name '.
            'FROM author JOIN books ON author.id = books.author '.
            "WHERE LOWER(author.name) = '".$author_name."' ". $order_by;

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