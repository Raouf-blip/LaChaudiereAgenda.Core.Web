<?php
namespace Chaudiere\core\actions\get;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Chaudiere\core\UseCase\AuthnService;
use Slim\Routing\RouteContext;

class GetCreateAdminUserAction
{
    private AuthnService $authnService;

    public function __construct(AuthnService $authnService)
    {
        $this->authnService = $authnService;
    }

    public function __invoke(Request $request, Response $response): Response
    {

        if (!$this->authnService->isSignedIn() || !$this->authnService->canManageUsers()) {

            $_SESSION['flash_message'] = 'Vous n\'avez pas les droits suffisants pour accéder à cette page.';
            $_SESSION['flash_message_type'] = 'error';

            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            $url = $routeParser->urlFor('home');
            return $response->withHeader('Location', $url)->withStatus(302);
        }

        $viewData = [];
        $httpMethod = $request->getMethod();
        $view = Twig::fromRequest($request);

        if ($httpMethod === 'POST') {
            $data = $request->getParsedBody();
            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';
            $confirmPassword = $data['confirm_password'] ?? '';
            $role = (int)($data['role'] ?? AuthnService::ROLE_ADMIN);

            if (empty($email) || empty($password) || empty($confirmPassword)) {
                $viewData['error_message'] = "Tous les champs sont requis.";
            } elseif ($password !== $confirmPassword) {
                $viewData['error_message'] = "Les mots de passe ne correspondent pas.";
            } else {
                try {
                    $user = $this->authnService->createAdminUser($email, $password, $role);

                    $_SESSION['flash_message'] = "L'utilisateur '{$user->email}' a été créé avec succès !";
                    $_SESSION['flash_message_type'] = 'success';

                    $routeParser = RouteContext::fromRequest($request)->getRouteParser();
                    $url = $routeParser->urlFor('home');
                    return $response->withHeader('Location', $url)->withStatus(302);

                } catch (\InvalidArgumentException $e) {
                    $viewData['error_message'] = "Erreur de saisie : " . $e->getMessage();
                } catch (\RuntimeException $e) {
                    $viewData['error_message'] = "Erreur : " . $e->getMessage();
                } catch (\Exception $e) {
                    error_log("Erreur lors de la création du compte: " . $e->getMessage());
                    $viewData['error_message'] = "Une erreur inattendue est survenue.";
                }
            }
            if (!isset($viewData['success_message']) && !isset($_SESSION['flash_message'])) {
                $viewData['email'] = $email;
            }
        }

        return $view->render($response, 'createAdmin.twig', $viewData);
    }
}