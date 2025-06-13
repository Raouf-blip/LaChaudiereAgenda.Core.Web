<?php
declare(strict_types=1);

namespace Chaudiere\core\actions;

use Chaudiere\core\domain\repositories\UserRepository; // You should remove this and inject it if you're using DI
use Chaudiere\core\providers\SessionAuthProvider;
use Chaudiere\core\UseCase\AuthnService;
use Chaudiere\core\UseCase\AuthnServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class SigninAction
{
    private AuthnServiceInterface $authnService;
    private SessionAuthProvider $authProvider;

    // Use Dependency Injection for the constructor
    // Your index.php should configure the container to pass these.
    public function __construct(AuthnServiceInterface $authnService, SessionAuthProvider $authProvider)
    {
        $this->authnService = $authnService;
        $this->authProvider = $authProvider;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $view = Twig::fromRequest($request);
        $viewData = []; // To pass data to Twig, e.g., error messages

        // IMPORTANT: Check authentication status using your authProvider
        // If already signed in, redirect to home
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
                // AuthnService::verifyCredentials already handles setting the session
                // by calling $this->authProvider->setActiveUserId() on success.
                $user = $this->authnService->verifyCredentials($email, $password);

                if ($user) {
                    // Authentication successful, session is already set by verifyCredentials
                    $_SESSION['flash_message'] = 'Connexion réussie ! Bienvenue.';
                    $_SESSION['flash_message_type'] = 'success';

                    $url = $routeParser->urlFor('home'); // Redirect to your home page
                    return $response->withHeader('Location', $url)->withStatus(302);

                } else {
                    // Authentication failed (email/password mismatch)
                    $_SESSION['flash_message'] = 'Email ou mot de passe invalide.';
                    $_SESSION['flash_message_type'] = 'error';
                    // The FlashMessageMiddleware will display this on the signin.twig page.
                    // No redirect needed here, just fall through to rendering the form.
                }

            } catch (\InvalidArgumentException $e) {
                // Catch validation errors (e.g., empty email/password) from AuthnService
                $_SESSION['flash_message'] = "Erreur de saisie : " . $e->getMessage();
                $_SESSION['flash_message_type'] = 'error';
            } catch (\Exception $e) {
                // Catch any other unexpected errors
                error_log("Signin error: " . $e->getMessage()); // Log the actual error
                $_SESSION['flash_message'] = "Une erreur inattendue est survenue lors de la connexion.";
                $_SESSION['flash_message_type'] = 'error';
            }
        }

        // For GET requests or POST requests that failed validation/authentication,
        // render the signin form. Flash messages will be handled by FlashMessageMiddleware.
        return $view->render($response, 'signin.twig', $viewData);
    }
}