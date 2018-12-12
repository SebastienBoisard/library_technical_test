<?php

namespace Library\Rest;

use Library\Util\Logger;
use Library\Util\Database;
use Library\Model\AuthorRepository;
use Library\Model\BookRepository;
use Library\Model\Book;
use Library\Rest\RestResponse;

class AuthorsActions
{
    public function handleGetBooksFromAuthorAction(array $path_params, array $body_params) : RestResponse
    {
        // Le Path doit contenir au moins un paramètre.
        if (count($path_params) != 1) {
            $response = new RestResponse();

            $response->setCode(RestResponse::STATUS_BAD_REQUEST);
            $response->setData(null);

            return $response;           
        }

        // Le paramètre du path doit être le nom de l'auteur.
        if (isset($path_params['name']) == false) {
            $response = new RestResponse();

            $response->setCode(RestResponse::STATUS_BAD_REQUEST);
            $response->setData(null);

            return $response;           
        }

        $author_name = $path_params['name'];

        // Vérifie que le paramètre "nom de l'auteur" soit une chaîne de caractères composées de minuscules avec 
        // potentiellement des undescores à la place des espaces.
        if (preg_match('/^[a-z_]*$/', $author_name) != 1) {
            $response = new RestResponse();

            $response->setCode(RestResponse::STATUS_BAD_REQUEST);
            $response->setData(null);

            return $response;                       
        }

        // L'ordre de tri des résultats est par défaut par titre du livre.
        $order = 'title';

        // Vérifie si la requête contient un paramètre dans son corps.
        if (count($body_params) != 0) {

            // S'il y a un paramètre dans le body, il ne devrait n'y en avoir qu'un seul : 'order'
            if (count($body_params) != 1) {
                $response = new RestResponse();

                $response->setCode(RestResponse::STATUS_BAD_REQUEST);
                $response->setData(null);

                return $response;           
            }

            if (isset($body_params['order']) == false) {
                $response = new RestResponse();

                $response->setCode(RestResponse::STATUS_BAD_REQUEST);
                $response->setData(null);

                return $response;           
            }

            $order = $body_params['order'];

            // Les valeurs du paramètre "order" ne peuvent être que 'id' ou 'title'
            if ($order != 'id' && $order != 'title') {
                $response = new RestResponse();

                $response->setCode(RestResponse::STATUS_BAD_REQUEST);
                $response->setData(null);

                return $response;           
            }
        }

        $data = $this->getAuthorBooks($author_name, $order);

        $response = new RestResponse();

        $response->setCode(RestResponse::STATUS_OK);
        $response->setData($data);

        return $response;
    }

    public function getAuthorBooks(string $author_name, string $order) : array
    {
        // All the whitespace were replaced by an underscore in the author name in the url, so we have to reverse this change.
        $author_name = str_replace('_', ' ', $author_name);
        
        $db = new Database();

        $db_conn = $db->getConnection();

        $book_repo = new AuthorRepository($db_conn);

        $all_books = $book_repo->getAuthorBooks($author_name, $order);

        $db->closeConnection();

        $data = [];
        foreach ($all_books as $book) {
            $data[] = $book->toArray();
        }

        return $data;   
    }
}