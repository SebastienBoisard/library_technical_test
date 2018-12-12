<?php

namespace Library;

use PHPUnit\Framework\TestCase;
use Library\Rest\BooksActions;
use Library\Rest\RestResponse;
use Library\Util\Logger;


class BooksActionsTest extends TestCase
{
    public function setUp()
    {
        // Disable the logger.
        Logger::getInstance()->disable();
    }

    /**
     * url: GET /books
     */
    public function testGetAllBooks()
    {
        $book_actions = new BooksActions();

        $response = $book_actions->handleGetBooksAction([], []);

        self::assertEquals(RestResponse::STATUS_OK, $response->getCode());
        self::assertGreaterThan(8, count($response->getData()));
    }

    /**
     * url: GET /books/1
     */
    public function testGetARealBook()
    {
        $book_actions = new BooksActions();

        $response = $book_actions->handleGetBookWitdIdAction(['id' => '1'], []);

        self::assertEquals(RestResponse::STATUS_OK, $response->getCode());
        self::assertEquals(1, $response->getData()['id']);
        self::assertEquals('book', $response->getData()['type']);
        self::assertEquals('The Colour of Magic', $response->getData()['title']);
        self::assertEquals('Terry Pratchett', $response->getData()['author']['name']);
    }

    /**
     * url: GET /books/123
     */
    public function testGetAnUnknownBook()
    {        
        $book_actions = new BooksActions();

        $response = $book_actions->handleGetBookWitdIdAction(['id' => '123'], []);

        self::assertEquals(RestResponse::STATUS_OK, $response->getCode());
        self::assertEquals([], $response->getData());
    }

    /**
     * url: GET /books/abc
     */
    public function testGetBookWithNonNumericId()
    {        
        $book_actions = new BooksActions();

        $response = $book_actions->handleGetBookWitdIdAction(['id' => 'abc'], []);

        self::assertEquals(RestResponse::STATUS_BAD_REQUEST, $response->getCode());
        self::assertEquals(null, $response->getData());
    }

    /**
     * url: GET /books/1/nothing
     */
    public function testGetBookWithTooManyParameters()
    {        
        $book_actions = new BooksActions();

        $response = $book_actions->handleGetBookWitdIdAction(['id' => '1', 'name' => 'nothing'], []);

        self::assertEquals(RestResponse::STATUS_BAD_REQUEST, $response->getCode());
        self::assertEquals(null, $response->getData());
    }

    /**
     * url: POST /books {"author":1,"title":"I, Robot"}
     */
    public function testPostBook()
    {        
        $book_actions = new BooksActions();

        $response = $book_actions->handlePostBookAction([], ['title' => 'I, Robot', 'author' => 1]);

        self::assertEquals(RestResponse::STATUS_OK, $response->getCode());
        self::assertEquals('I, Robot', $response->getData()['title']);
        self::assertEquals('Isaac Asimov', $response->getData()['author']['name']);
    }

    /**
     * url: POST /books {"author":1}
     */
    public function testPostBookWithMissingParameters()
    {        
        $book_actions = new BooksActions();

        $response = $book_actions->handlePostBookAction([], ['title' => 'I, Robot']);

        self::assertEquals(RestResponse::STATUS_BAD_REQUEST, $response->getCode());
        self::assertEquals(null, $response->getData());
    }

    /**
     * url: POST /books {"author":"Asimov","title":"I, Robot"}
     */
    public function testPostBookWithWrongAuthorId()
    {        
        $book_actions = new BooksActions();

        $response = $book_actions->handlePostBookAction([], ['title' => 'I, Robot', 'author' => 'asimov']);

        self::assertEquals(RestResponse::STATUS_BAD_REQUEST, $response->getCode());
        self::assertEquals(null, $response->getData());
    }
}
