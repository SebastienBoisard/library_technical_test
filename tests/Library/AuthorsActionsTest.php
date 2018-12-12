<?php

namespace Library;

use PHPUnit\Framework\TestCase;
use Library\Rest\AuthorsActions;
use Library\Rest\RestResponse;
use Library\Util\Logger;


class AuthorsActionsTest extends TestCase
{
    public function setUp()
    {
        // Disable the logger.
        Logger::getInstance()->disable();
    }

    /**
     * url: GET /authors/terry_pratchett/books
     * curl: curl -v -X GET http://library/authors/terry_pratchett/books 
     */
    public function testGetAllBooksFromAuthor()
    {
        $author_actions = new AuthorsActions();

        $response = $author_actions->handleGetBooksFromAuthorAction(['name' => 'terry_pratchett'], []);

        self::assertEquals(RestResponse::STATUS_OK, $response->getCode());
        self::assertEquals(3, count($response->getData()));
    }

    /**
     * url: GET /authors/terry_pratchett/books {"order":"id"}
     * curl: curl -v -X GET -d '{"order":"id"}' http://library/authors/terry_pratchett/books 
     */
    public function testGetAllBooksFromAuthorOrderById()
    {
        $author_actions = new AuthorsActions();

        $response = $author_actions->handleGetBooksFromAuthorAction(['name' => 'terry_pratchett'], ['order' => 'id']);

        self::assertEquals(RestResponse::STATUS_OK, $response->getCode());
        self::assertEquals(1, $response->getData()[0]['id']);
        self::assertEquals(2, $response->getData()[1]['id']);
        self::assertEquals(3, $response->getData()[2]['id']);
    }

    /**
     * url: GET /authors/terry_pratchett/books {"order":"title"}
     * curl: curl -v -X GET -d '{"order":"title"}' http://library/authors/terry_pratchett/books 
     */
    public function testGetAllBooksFromAuthorOrderByTitle()
    {
        $author_actions = new AuthorsActions();

        $response = $author_actions->handleGetBooksFromAuthorAction(['name' => 'terry_pratchett'], ['order' => 'title']);

        self::assertEquals(RestResponse::STATUS_OK, $response->getCode());
        self::assertEquals('Going Postal',        $response->getData()[0]['title']);
        self::assertEquals('The Colour of Magic', $response->getData()[1]['title']);
        self::assertEquals('Thief of Time',       $response->getData()[2]['title']);
    }
}