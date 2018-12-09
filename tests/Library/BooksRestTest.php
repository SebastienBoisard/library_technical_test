<?php

namespace Library;

use PHPUnit\Framework\TestCase;
use Library\Rest\BooksRest;
use Library\Rest\RestResponse;
use Library\Util\Logger;

class BooksRestTest extends TestCase
{
    public function setUp() {
        Logger::getInstance()->disable();
    }

    /**
     * url: /books/1
     */
    public function testGetARealBook() {

        $book_endpoint = new BooksRest();

        $response = $book_endpoint->get(['id' => '1']);

        self::assertEquals(RestResponse::STATUS_OK, $response->getCode());
        self::assertEquals(1, $response->getData()['id']);
        self::assertEquals('book', $response->getData()['type']);
        self::assertEquals('The Colour of Magic', $response->getData()['title']);
        self::assertEquals('Terry Pratchett', $response->getData()['author']['name']);
    }

    /**
     * url: /books
     */
    public function testAllBooks() {

        $book_endpoint = new BooksRest();

        $response = $book_endpoint->get([]);

        self::assertEquals(RestResponse::STATUS_OK, $response->getCode());
        self::assertEquals(8, count($response->getData()));
    }

    /**
     * url: /books/123
     */
    public function testGetAnUnknownBook() {
        
        $book_endpoint = new BooksRest();

        $response = $book_endpoint->get(['id' => '123']);

        self::assertEquals(RestResponse::STATUS_OK, $response->getCode());
        self::assertEquals([], $response->getData());
    }

    /**
     * url: /books/abc
     */
    public function testGetBookWithNonNumericId() {
        
        $book_endpoint = new BooksRest();

        $response = $book_endpoint->get(['id' => 'abc']);

        self::assertEquals(RestResponse::STATUS_BAD_REQUEST, $response->getCode());
        self::assertEquals(null, $response->getData());
    }

    /**
     * url: /books/1/nothing
     */
    public function testGetBookWithTooManyParameters() {
        
        $book_endpoint = new BooksRest();

        $response = $book_endpoint->get(['id' => '1', 'name' => 'nothing']);

        self::assertEquals(RestResponse::STATUS_BAD_REQUEST, $response->getCode());
        self::assertEquals(null, $response->getData());
    }
}
