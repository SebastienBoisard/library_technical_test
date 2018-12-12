<?php

namespace Library\Model;

use Library\Model\Author;
use Library\Model\Book;
use Library\Util\Logger;


class BookRepository
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
     * getBook returns a book from its id.
     *
     * @param int $bookId  - the book id 
     * @return Book - the matching book
     * @throws Exception
     */
    public function getBook(int $bookId) : ?Book
    {
        $query = 'SELECT books.id as book_id, books.name as book_name, author.id as author_id, author.name as author_name '.
            'FROM books JOIN author ON books.author = author.id '.
            'WHERE books.id = '.$bookId;


        $result = $this->db_conn->query($query);

        if ($result === false) {
            throw new \Exception("Database error with query `".$query."` (error=".$mysqli->error.")");
        }

        if ($result->num_rows == 0) {
            // No matching book found
            return null;
        } 

        if ($result->num_rows > 1) {
            // Too many books found
            throw new \Exception("Database error with query `".$query."`: too many books found");
        } 

        $row = $result->fetch_array();
        
        $book = new Book($row['book_id'], $row['book_name']);

        $author = new Author($row['author_id'], $row['author_name']);

        $book->setAuthor($author);

        $result->free();

        return $book;
    }

    /**
     * getBooks returns all the books.
     *
     * @return Array - all the books
     * @throws Exception
     */
    public function getBooks() : Array
    {
        $query = 'SELECT books.id as book_id, books.name as book_name, author.id as author_id, author.name as author_name '.
            'FROM books JOIN author ON books.author = author.id';

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
            $book = new Book($row['book_id'], $row['book_name']);

            $author = new Author($row['author_id'], $row['author_name']);

            $book->setAuthor($author);

            $books[] = $book;
        }


        $result->free();

        return $books;
    }

    /**
     * createBook creates a book (and inserts it in the database) from its title and its author id.
     *
     * @return Book - the new created book
     * @throws Exception
     */
    public function createBook(string $book_title, int $author_id) : Book
    {
        // Sanitize the book_title
        $book_title = $this->db_conn->real_escape_string($book_title);

        $query = "INSERT INTO books (name, author) VALUES ('".$book_title."', ".$author_id.")";

        $result = $this->db_conn->query($query);

        if ($result === false) {
            throw new \Exception("Database error with query `".$query."` (error=".$mysqli->error.")");
        }

        // Get the id of the last insert, that means the book id.
        $book_id = $this->db_conn->insert_id;

        $book = $this->getBook($book_id);

        return $book;
    }
}
