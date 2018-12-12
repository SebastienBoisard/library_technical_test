<?php

namespace Library\Routing;

use Library\Util\Logger;
use Library\Rest\RestInterface;

class Router
{
    public const GET    = 'GET';
    public const POST   = 'POST';
    public const PUT    = 'PUT';
    public const DELETE = 'DELETE';

    private $routes;

    public function __construct()
    {
        $this->routes = [];
    }

    private function convertRoutePattern(string $route) : string
    {
        // Vérifie que la route est composée par les caractères admis
        if (preg_match('/[^-\/_a-zA-Z\d:]/', $route)) {
            // Route invalide
            throw new \Exception("invalid route");
        }

        // Remplace les paramètres (i.e :parameter) par une expression régulière (i.e. (?<parameter>[a-zA-Z0-9\_\-]+)")
        // Utilisation du nom de groupe (?<group_name>...) pour arriver à ce résultat.
        $route = preg_replace(
            '/:([a-zA-Z0-9\_\-]+)/',   # Remplace ":parameter"
            '(?<$1>[a-zA-Z0-9\_\-]+)', # par "(?<parameter>[a-zA-Z0-9\_\-]+)"
            $route
        );

        // Ajoute les regex pour le début et la fin d'une chaîne
        // @ est choisi comme séparateur de la regex
        $route_as_regex = "@^" . $route . "$@";

        return $route_as_regex;
    }

    public function addRoute(string $verb, string $route, string $endpoint_class_name, string $endpoint_function_name)
    {
        $route_as_regex = $this->convertRoutePattern($route);

        $this->routes[] = new Route($verb, $route_as_regex, $endpoint_class_name, $endpoint_function_name);
    }

    public function handleRoutes()
    {
        $current_verb = $_SERVER['REQUEST_METHOD'];
        $current_route = $_SERVER['REQUEST_URI'];

        foreach ($this->routes as $route) {
            if ($route->getVerb() != $current_verb) {
                continue;
            }

            // Gestion des paramètres passés dans le path de l'url.
            $ok = preg_match($route->getUrl(), $current_route, $matches);

            if ($ok != 1) {
                continue;
            }

            // Extrait toutes les variables du tableau des correspondances issues
            // de la regex (seules les clés "string" sont à extraire)
            $path_params = [];
            foreach ($matches as $key => $value) {
                if (is_string($key) == true) {
                    $path_params[$key] = $value;
                }
            }

            // Récupération des paramètres passés en JSon dans le corps de la requête
            $body_params = json_decode(file_get_contents("php://input"), true);
            if ($body_params == null) {
                $body_params = [];
            }

            $a = $route->getEndpointClassName();
            $endpoint = new $a;
            $response = $endpoint->{strtolower($route->getEndpointFunctionName())}($path_params, $body_params);

            http_response_code($response->getCode());

            $data = [];
            $data['data'] = $response->getData();

            print(json_encode($data));

            return;
        }
    }
}