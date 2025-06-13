<?php

namespace Chaudiere\middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
use Slim\Routing\RouteContext;

class AuthMiddleware implements MiddlewareInterface
{
    private $authProvider;
    private $publicRoutes;

    public function __construct($authProvider, array $publicRoutes = [])
    {
        $this->authProvider = $authProvider;
        // Routes publiques qui ne nécessitent pas d'authentification
        $this->publicRoutes = array_merge([
            'signin',
            'signin_post',
            'signup',
            'signup_post',
            'signout',
        ], $publicRoutes);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        // Si on ne peut pas déterminer la route, on laisse passer
        if (!$route) {
            return $handler->handle($request);
        }

        $routeName = $route->getName();

        // Si c'est une route publique, on laisse passer
        if (in_array($routeName, $this->publicRoutes)) {
            return $handler->handle($request);
        }

        // Vérifier si l'utilisateur est connecté
        if (!$this->authProvider->isAuthenticated()) {
            // Redirection vers la page de connexion
            $response = new Response();
            return $response
                ->withHeader('Location', '/signin')
                ->withStatus(302);
        }

        // L'utilisateur est connecté, on continue
        return $handler->handle($request);
    }
}