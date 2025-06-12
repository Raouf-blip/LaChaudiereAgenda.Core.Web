<?php
declare(strict_types=1);

namespace Chaudiere\core\middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Views\Twig;

class FlashMessageMiddleware implements MiddlewareInterface
{
    private Twig $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        // On récupère les messages flash de la session
        $flashMessage = $_SESSION['flash_message'] ?? null;
        $flashMessageType = $_SESSION['flash_message_type'] ?? 'info';

        // On efface les messages de la session IMMÉDIATEMENT après les avoir lus
        // C'est essentiel pour qu'ils ne s'affichent qu'une seule fois.
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_message_type']);

        // On les rend disponibles à Twig en tant que variables globales
        // Cela garantit que toutes tes vues Twig qui étendent 'layout.twig' y auront accès.
        $this->twig->getEnvironment()->addGlobal('flash_message', $flashMessage);
        $this->twig->getEnvironment()->addGlobal('flash_message_type', $flashMessageType);

        // On passe la main au prochain gestionnaire (middleware ou route)
        $response = $handler->handle($request);

        return $response;
    }
}