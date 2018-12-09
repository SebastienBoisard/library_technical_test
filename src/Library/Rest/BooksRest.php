<?php

namespace Library\Rest;

use Library\Util\Logger;
use Library\Util\Database;
use Library\Model\BookRepository;
use Library\Model\Book;
use Library\Rest\RestInterface;
use Library\Rest\RestResponse;


class BooksRest implements RestInterface
{
	public function get(array $params) : RestResponse {

		if (count($params) > 1) {
			$response = new RestResponse();

			$response->setCode(RestResponse::STATUS_BAD_REQUEST);
			$response->setData(null);

			return $response;			
		}

		if (count($params) == 0) {

			$data = $this->getBooks();

			$response = new RestResponse();

			$response->setCode(RestResponse::STATUS_OK);
			$response->setData($data);

			return $response;
		}

		// Vérifie que le paramètre soit un entier.
		if (preg_match('/^\d*$/', $params['id']) != 1) {
			$response = new RestResponse();

			$response->setCode(RestResponse::STATUS_BAD_REQUEST);
			$response->setData(null);

			return $response;						
		}

		$data = $this->getBook((int) $params['id']);

		$response = new RestResponse();

		$response->setCode(RestResponse::STATUS_OK);
		$response->setData($data);

		return $response;
	}

	public function getBooks() : array {

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

	public function getBook(int $book_id) : array {

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
}
