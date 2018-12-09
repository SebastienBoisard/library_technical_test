<?php

namespace Library\Rest;

use Library\Util\Logger;
use Library\Util\Database;
use Library\Model\AuthorRepository;
use Library\Model\BookRepository;
use Library\Model\Book;
use Library\Rest\RestInterface;
use Library\Rest\RestResponse;

class AuthorsRest implements RestInterface
{
	public function get(array $params) : RestResponse {

		if (count($params) != 1) {
			$response = new RestResponse();

			$response->setCode(RestResponse::STATUS_BAD_REQUEST);
			$response->setData(null);

			return $response;			
		}

		// Vérifie que le paramètre soit un entier.
		if (preg_match('/^[a-z_]*$/', $params['name']) != 1) {
			$response = new RestResponse();

			$response->setCode(RestResponse::STATUS_BAD_REQUEST);
			$response->setData(null);

			return $response;						
		}

		$data = $this->getAuthorBooks($params['name']);

		$response = new RestResponse();

		$response->setCode(RestResponse::STATUS_OK);
		$response->setData($data);

		return $response;
	}

	public function getAuthorBooks(string $author_name) : array {

		// All the whitespace were replaced by an underscore in the author name in the url, so we have to reverse this change.
		$author_name = str_replace('_', ' ', $author_name);
		
		$db = new Database();

		$db_conn = $db->getConnection();

		$book_repo = new AuthorRepository($db_conn);

		$all_books = $book_repo->getAuthorBooks($author_name);

		$db->closeConnection();

		$data = [];
		foreach ($all_books as $book) {
			$data[] = $book->toArray();
		}

		return $data;	
	}
}
