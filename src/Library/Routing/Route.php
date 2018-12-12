<?php

namespace Library\Routing;

use Library\Util\Logger;

class Route
{
    private $verb;
    private $url;
    private $endpoint_class_name;
    private $endpoint_function_name;

    public function __construct(string $verb, string $url, string $endpoint_class_name, string $endpoint_function_name)
    {
        $this->verb = $verb;
        $this->url = $url;
        $this->endpoint_class_name = $endpoint_class_name;
        $this->endpoint_function_name = $endpoint_function_name;
    }

    public function getVerb() : string
    {
        return $this->verb;
    }

    public function getUrl() : string
    {
        return $this->url;
    }

    public function getEndPointClassName() : string
    {
        return $this->endpoint_class_name;
    }

    public function getEndPointFunctionName() : string
    {
        return $this->endpoint_function_name;
    }
}   
