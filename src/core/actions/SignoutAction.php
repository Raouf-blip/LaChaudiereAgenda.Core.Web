<?php
declare(strict_types=1);

namespace Chaudiere\core\actions;

use Chaudiere\core\domain\repositories\UserRepository;
use Chaudiere\core\providers\SessionAuthProvider;
use Chaudiere\core\UseCase\AuthnService;
use Chaudiere\core\UseCase\AuthnServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;

class SignoutAction
{
    private AuthnServiceInterface $authnService;

    public function __construct()
    {
        $userRepository = new UserRepository();
        $authProvider = new SessionAuthProvider($userRepository);
        $this->authnService = new AuthnService($userRepository, $authProvider);
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $this->authnService->signOut(); // This will call authProvider->signOut()

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $url = $routeParser->urlFor('home'); // Assuming 'home' is the name of your home route
        return $response->withHeader('Location', $url)->withStatus(302);
    }
}