<?php

namespace Library\Routing;

use Library\Util\Logger;
use Library\Rest\RestInterface;

class Router
{
	public const GET = 'GET';
	public const POST = 'POST';
	public const PUT = 'PUT';
	public const DELETE = 'DELETE';

	private $routes;

	public function __construct() {

		$this->routes = [];
	}

	private function convertRoutePattern(string $route) : string {

    	// Vérifie que la route est composée par les caractères admis
    	if (preg_match('/[^-\/_a-zA-Z\d:]/', $route)) {
        	// Route invalide
        	throw new \Exception("invalid route");
    	}

    	// Create capture group for ":parameter"
    	// Remplace les paramètres (i.e :parameter) par une expression régulière (i.e. (?<parameter>[a-zA-Z0-9\_\-]+)")
    	$route = preg_replace(
        	'/:([a-zA-Z0-9\_\-]+)/',   # Remplace ":parameter"
        	'(?<$1>[a-zA-Z0-9\_\-]+)', # par "(?<parameter>[a-zA-Z0-9\_\-]+)"
        	$route
    	);

    	// Ajoute les regex pour le début et la fin d'une chaîne
    	// @ est choisi comme séparateur de la regex
    	$patternAsRegex = "@^" . $route . "$@";

    	return $patternAsRegex;
	}

	public function addRoute(string $verb, string $route, string $endpoint_classname) {

		$route_as_regex = $this->convertRoutePattern($route);

		$this->routes[] = new Route($verb, $route_as_regex, $endpoint_classname);
	}

	public function handleRoutes() {

		$current_verb = $_SERVER['REQUEST_METHOD'];
		$current_route = $_SERVER['REQUEST_URI'];

		Logger::getInstance()->addDebug("current_verb=$current_verb   current_route=$current_route");

		foreach ($this->routes as $route) {
			if ($route->getVerb() != $current_verb) {
				Logger::getInstance()->addDebug("current_verb=$current_verb   route->getVerb()=".$route->getVerb());
				continue;
			}

			$ok = preg_match($route->getUrl(), $current_route, $matches);

			if ($ok != 1) {
				Logger::getInstance()->addDebug("ok=$ok");
				continue;
			}

		    // Extrait toutes les variables du tableau des correspondances issues
		    // de la regex (seules les clés "string" sont à extraire)
		    $params = [];
		    foreach ($matches as $key => $value) {
		        if (is_string($key) == true) {
		            $params[$key] = $value;
		        }
		    }

			Logger::getInstance()->addDebug("params=".var_export($params, true));
			Logger::getInstance()->addDebug("route->getEndpointClassName=".var_export($route->getEndpointClassName(), true));

			$a = $route->getEndpointClassName();
			$endpoint = new $a;
			$response = $endpoint->{strtolower($current_verb)}($params);

			http_response_code($response->getCode());

			$data = [];
			$data['data'] = $response->getData();

	    	print(json_encode($data));

	    	return;
		}
	}
}
