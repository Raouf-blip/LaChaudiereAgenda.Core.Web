<?php
declare(strict_types=1);

namespace Chaudiere\core\actions;

use Chaudiere\core\UseCase\AuthnServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;

class SignoutAction
{
    private AuthnServiceInterface $authnService;

    public function __construct(AuthnServiceInterface $authnService)
    {
        $this->authnService = $authnService;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        // Déconnexion
        $this->authnService->signOut();

        // Message flash
        $_SESSION['flash_message'] = 'Vous avez été déconnecté avec succès.';
        $_SESSION['flash_message_type'] = 'success';

        // Redirection vers la page de connexion
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $url = $routeParser->urlFor('signin');

        return $response->withHeader('Location', $url)->withStatus(302);
    }
}