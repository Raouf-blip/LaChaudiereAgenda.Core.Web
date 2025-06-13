<?php

namespace Chaudiere\controllers;

use Chaudiere\core\UseCase\AuthnService;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chaudiere\core\domain\entities\Events;
use Chaudiere\core\domain\entities\Categories;
use Chaudiere\core\domain\entities\Images;
use Slim\Psr7\UploadedFile;

class AdminController
{
    private AuthnService $authnService;

    public function __construct(AuthnService $authnService)
    {
        $this->authnService = $authnService;
    }

    public function createEvent(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $twig = Twig::fromRequest($request);
        $categories = Categories::all();
        $params = [];

        // Récupération des jetons CSRF
        $csrfNameKey = 'csrf_name';
        $csrfValueKey = 'csrf_value';
        $csrfName = $request->getAttribute($csrfNameKey);
        $csrfValue = $request->getAttribute($csrfValueKey);

        $params['csrf'] = [
            'keys' => [
                'name' => $csrfNameKey,
                'value' => $csrfValueKey
            ],
            'name' => $csrfName,
            'value' => $csrfValue
        ];

        if (!$this->authnService->isSignedIn() || !($this->authnService->canCreate() || $this->authnService->canManageUsers())) {

            $_SESSION['flash_message'] = 'Vous n\'avez pas les droits suffisants pour accéder à cette page.';
            $_SESSION['flash_message_type'] = 'error';

            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            $url = $routeParser->urlFor('home');
            return $response->withHeader('Location', $url)->withStatus(302);
        }

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            $imageId = null;
            $uploadedFiles = $request->getUploadedFiles();
            $uploadedFile = $uploadedFiles['image_file'] ?? null;

            if ($uploadedFile instanceof UploadedFile && $uploadedFile->getError() === UPLOAD_ERR_OK) {
                $uploadDirectory = __DIR__ . '/../../public/img';

                if (!is_dir($uploadDirectory)) {
                    mkdir($uploadDirectory, 0777, true);
                }

                $filename = $this->moveUploadedFile($uploadDirectory, $uploadedFile);

                // Génère une URL accessible par l'app mobile
                $publicUrl = $_ENV['APP_URL'] . '/img/' . $filename;

                $image = Images::create([
                    'id'   => uniqid(),
                    'name' => $filename,
                    'url'  => $publicUrl
                ]);

                $imageId = $image->id;
            }

            Events::create([
                'id'           => uniqid(),
                'title'        => $data['title'],
                'description'  => $data['description'],
                'start_date'   => $data['start_date'],
                'end_date'     => $data['end_date'],
                'start_time'   => $data['start_time'] ?: null,
                'end_time'     => $data['end_time'] ?: null,
                'price'        => $data['price'] ?: 0,
                'image_id'     => $imageId,
                'category_id'  => $data['category_id'],
                'created_by'   => null,
                'is_published' => isset($data['is_published'])
            ]);

            $params['success'] = true;
        }

        $params['categories'] = $categories;
        return $twig->render($response, 'create_event.twig', $params);
    }


    private function moveUploadedFile(string $directory, UploadedFile $uploadedFile): string
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8));
        $filename = sprintf('%s.%s', $basename, $extension);

        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }

    public function createCategory(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $twig = Twig::fromRequest($request);
        $params = [];

        // CSRF
        $csrfNameKey = 'csrf_name';
        $csrfValueKey = 'csrf_value';
        $csrfName = $request->getAttribute($csrfNameKey);
        $csrfValue = $request->getAttribute($csrfValueKey);

        $params['csrf'] = [
            'keys' => [
                'name' => $csrfNameKey,
                'value' => $csrfValueKey
            ],
            'name' => $csrfName,
            'value' => $csrfValue
        ];

        if (!$this->authnService->isSignedIn() || !($this->authnService->canCreate() || $this->authnService->canManageUsers())) {

            $_SESSION['flash_message'] = 'Vous n\'avez pas les droits suffisants pour accéder à cette page.';
            $_SESSION['flash_message_type'] = 'error';

            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            $url = $routeParser->urlFor('home');
            return $response->withHeader('Location', $url)->withStatus(302);
        }

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();
            $name = trim($data['name'] ?? '');
            $description = trim($data['description'] ?? '');

            if ($name === '' || $description === '') {
                $params['error'] = 'Veuillez remplir tous les champs.';
                $params['old'] = $data;
            } else {
                Categories::create([
                    'name' => $name,
                    'description' => $description
                ]);
                $params['success'] = true;
                $_SESSION['flash_message'] = "L'evénement à été crée avec succès!";
                $_SESSION['flash_message_type'] = 'success';
            }
        }

        return $twig->render($response, 'create_category.twig', $params);
    }
}