<?php

namespace Library\Rest;

use Library\Util\Logger;
use Library\Util\Database;
use Library\Model\BookRepository;
use Library\Model\Book;
use Library\Rest\RestResponse;


class BooksActions
{
    public function handleGetBooksAction(array $path_params, array $body_params) : RestResponse
    {
        // Cette action ne doit avoir aucun paramètre dans son path.
        if (count($path_params) != 0) {
            $response = new RestResponse();

            $response->setCode(RestResponse::STATUS_BAD_REQUEST);
            $response->setData(null);

            return $response;           
        }

        $data = $this->getBooks();

        $response = new RestResponse();

        $response->setCode(RestResponse::STATUS_OK);
        $response->setData($data);

        return $response;
    }

    public function handleGetBookWitdIdAction(array $path_params, array $body_params) : RestResponse
    {
        // Cette action ne peut avoir qu'un seul paramètre dans son path.
        if (count($path_params) != 1) {
            $response = new RestResponse();

            $response->setCode(RestResponse::STATUS_BAD_REQUEST);
            $response->setData(null);

            return $response;           
        }

        if (isset($path_params) == false) {
            $response = new RestResponse();

            $response->setCode(RestResponse::STATUS_BAD_REQUEST);
            $response->setData(null);

            return $response;           
        }

        // Vérifie que le paramètre `id` soit un entier.
        if (preg_match('/^\d*$/', $path_params['id']) != 1) {
            $response = new RestResponse();

            $response->setCode(RestResponse::STATUS_BAD_REQUEST);
            $response->setData(null);

            return $response;                       
        }

        $book_id = (int) $path_params['id'];

        $data = $this->getBook($book_id);

        $response = new RestResponse();

        $response->setCode(RestResponse::STATUS_OK);
        $response->setData($data);

        return $response;
    }

    public function handlePostBookAction(array $path_params, array $body_params) : RestResponse
    {
        // Cette action ne doit avoir aucun paramètre dans son path.
        if (count($path_params) != 0) {
            $response = new RestResponse();

            $response->setCode(RestResponse::STATUS_BAD_REQUEST);
            $response->setData(null);

            return $response;           
        }

        // Cette action doit avoir 2 paramètres dans le corps de la requête.
        if (count($body_params) != 2) {
            $response = new RestResponse();

            $response->setCode(RestResponse::STATUS_BAD_REQUEST);
            $response->setData(null);

            return $response;           
        }

        // Les 2 paramètres de cette action sont 'title' et 'author'.
        if (isset($body_params['title']) == false || isset($body_params['author']) == false) {
            $response = new RestResponse();

            $response->setCode(RestResponse::STATUS_BAD_REQUEST);
            $response->setData(null);

            return $response;           
        } 

        // Vérifie que le paramètre `author` soit un entier.
        if (preg_match('/^\d*$/', $body_params['author']) != 1) {
            $response = new RestResponse();

            $response->setCode(RestResponse::STATUS_BAD_REQUEST);
            $response->setData(null);

            return $response;                       
        }

        $book_title = $body_params['title'];
        $author_id = (int) $body_params['author'];

        $data = $this->createBook($book_title, $author_id);

        $response = new RestResponse();

        $response->setCode(RestResponse::STATUS_OK);
        $response->setData($data);

        return $response;
    }

    public function getBooks() : array
    {
        $db = new Database();

        $db_conn = $db->getConnection();

        $book_repo = new BookRepository($db_conn);

        $all_books = $book_repo->getBooks();

        $db->closeConnection();

        $data = [];
        foreach ($all_books as $book) {
            $data[] = $book->toArray();
        }

        return $data;   
    }

    public function getBook(int $book_id) : array
    {
        $db = new Database();

        $db_conn = $db->getConnection();

        $book_repo = new BookRepository($db_conn);

        $wanted_book = $book_repo->getBook($book_id);

        $db->closeConnection();

        if ($wanted_book != null) {
            return $wanted_book->toArray();
        } else {
            return [];
        }
    }

    public function createBook(string $book_title, int $author_id) : array
    {
        $db = new Database();
        $db_conn = $db->getConnection();

        $book_repo = new BookRepository($db_conn);

        $book = $book_repo->createBook($book_title, $author_id);

        $db->closeConnection();        

        if ($book != null) {
            return $book->toArray();
        } else {
            return [];
        }
    }
}