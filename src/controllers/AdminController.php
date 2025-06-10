<?php

namespace Chaudiere\controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chaudiere\core\domain\entities\Events;
use Chaudiere\core\domain\entities\Categories;
use Chaudiere\core\domain\entities\Images;

class AdminController
{
    public function createEvent($request, $response, $args)
    {
        $twig = \Slim\Views\Twig::fromRequest($request);
        $categories = Categories::all();
        $params = [];

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            // Créer l'image si fournie
            $imageId = null;
            if (!empty($data['image_url'])) {
                $image = Images::create([
                    'id' => uniqid(),
                    'name' => $data['image_url']
                ]);
                $imageId = $image->id;
            }

            Events::create([
                'id' => uniqid(),
                'title' => $data['title'],
                'description' => $data['description'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'start_time' => $data['start_time'] ?: null,
                'end_time' => $data['end_time'] ?: null,
                'price' => $data['price'] ?: 0,
                'image_id' => $imageId,
                'category_id' => $data['category_id'],
                'created_by' => null,
                'is_published' => isset($data['is_published'])
            ]);

            $params['success'] = true;
        }

        $params['categories'] = $categories;
        return $twig->render($response, 'admin/create_event.twig', $params);
    }

}