<?php

namespace Chaudiere\controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chaudiere\core\domain\entities\Events;
use Chaudiere\core\domain\entities\Categories;
use Chaudiere\core\domain\entities\Images;
use Slim\Psr7\UploadedFile;

class AdminController
{
    public function createEvent($request, $response, $args)
    {
        $twig = \Slim\Views\Twig::fromRequest($request);
        $categories = Categories::all();
        $params = [];

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            $imageId = null;
            $uploadedFiles = $request->getUploadedFiles();
            $uploadedFile = $uploadedFiles['image_file'] ?? null;

            if ($uploadedFile instanceof UploadedFile && $uploadedFile->getError() === UPLOAD_ERR_OK) {
                $uploadDirectory = __DIR__ . '../../public/img';
                if (!is_dir($uploadDirectory)) {
                    mkdir($uploadDirectory, 0777, true);
                }

                $filename = moveUploadedFile($uploadDirectory, $uploadedFile);

                $image = new Images();
                $image->id = uniqid();
                $image->name = $filename;
                $image->save();

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

}