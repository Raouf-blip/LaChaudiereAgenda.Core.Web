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
use Slim\Views\Twig;

class SignupAction
{
    private AuthnServiceInterface $authnService;
    private SessionAuthProvider $authProvider;

    public function __construct()
    {
        $userRepository = new UserRepository();

        // Pass the userRepository to SessionAuthProvider constructor
        $this->authProvider = new SessionAuthProvider($userRepository); // Initialise authProvider ici

        $this->authnService = new AuthnService($userRepository, $this->authProvider);
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        if ($this->authProvider->isAuthenticated()) {
            $url = $routeParser->urlFor('home');
            return $response->withHeader('Location', $url)->withStatus(302);
        }

        if ($request->getMethod() === 'POST') {
            $params = $request->getParsedBody();
            $email = $params['email'] ?? '';
            $password = $params['password'] ?? '';
            $confirmPassword = $params['confirm_password'] ?? '';

            // Basic validation
            if (empty($email) || empty($password) || empty($confirmPassword)) {
                $view = Twig::fromRequest($request);
                return $view->render($response, 'signup.twig', ['error' => 'Tous les champs sont obligatoires.']);
            }

            if ($password !== $confirmPassword) {
                $view = Twig::fromRequest($request);
                return $view->render($response, 'signup.twig', ['error' => 'Les mots de passe ne correspondent pas.']);
            }

            try {
                $this->authnService->register($email, $password);

                $user = $this->authnService->verifyCredentials($email, $password);

                if ($user) {
                    $url = $routeParser->urlFor('home');
                    return $response->withHeader('Location', $url)->withStatus(302);
                } else {
                    $url = $routeParser->urlFor('signin');
                    return $response->withHeader('Location', $url)->withStatus(302);
                }

            } catch (\Exception $e) {

                $view = Twig::fromRequest($request);
                return $view->render($response, 'signup.twig', ['error' => $e->getMessage()]);
            }
        }

        $view = Twig::fromRequest($request);
        return $view->render($response, 'signup.twig');
    }
}