<?php
declare(strict_types=1);

namespace Chaudiere\core\actions;

use Chaudiere\core\providers\SessionAuthProvider;
use Chaudiere\core\UseCase\AuthnServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class SigninAction
{
    private AuthnServiceInterface $authnService;
    private SessionAuthProvider $authProvider;

    public function __construct(AuthnServiceInterface $authnService, SessionAuthProvider $authProvider)
    {
        $this->authnService = $authnService;
        $this->authProvider = $authProvider;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $view = Twig::fromRequest($request);
        $viewData = [];

        // Récupération des jetons CSRF à chaque affichage du formulaire
        $csrfNameKey = 'csrf_name';
        $csrfValueKey = 'csrf_value';
        $csrfName = $request->getAttribute($csrfNameKey);
        $csrfValue = $request->getAttribute($csrfValueKey);
        $viewData['csrf'] = [
            'keys' => [
                'name' => $csrfNameKey,
                'value' => $csrfValueKey
            ],
            'name' => $csrfName,
            'value' => $csrfValue
        ];

        // Vérifier si l'utilisateur est déjà connecté
        if ($this->authProvider->isAuthenticated()) {
            $_SESSION['flash_message'] = 'Vous êtes déjà connecté.';
            $_SESSION['flash_message_type'] = 'info';

            $url = $routeParser->urlFor('home');
            return $response->withHeader('Location', $url)->withStatus(302);
        }

        if ($request->getMethod() === 'POST') {
            $params = $request->getParsedBody();
            $email = $params['email'] ?? '';
            $password = $params['password'] ?? '';

            try {
                $user = $this->authnService->verifyCredentials($email, $password);

                if ($user) {
                    $this->authProvider->login($user);

                    $_SESSION['flash_message'] = 'Connexion réussie ! Bienvenue.';
                    $_SESSION['flash_message_type'] = 'success';

                    $url = $routeParser->urlFor('home');
                    return $response->withHeader('Location', $url)->withStatus(302);
                } else {
                    $_SESSION['flash_message'] = 'Email ou mot de passe invalide.';
                    $_SESSION['flash_message_type'] = 'error';
                }

            } catch (\InvalidArgumentException $e) {
                $_SESSION['flash_message'] = "Erreur de saisie : " . $e->getMessage();
                $_SESSION['flash_message_type'] = 'error';
            } catch (\Exception $e) {
                error_log("Signin error: " . $e->getMessage());
                $_SESSION['flash_message'] = "Une erreur inattendue est survenue lors de la connexion.";
                $_SESSION['flash_message_type'] = 'error';
            }
        }

        return $view->render($response, 'signin.twig', $viewData);
    }
}