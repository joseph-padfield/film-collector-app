<?php

namespace App\Controllers;

use App\Models\FilmsModel;
use App\Abstracts\Controller;
use PDOException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;

class UpdateFilmController extends Controller
{
    private FilmsModel $model;
    private PhpRenderer $renderer;

    public function __construct(FilmsModel $model, PhpRenderer $renderer)
    {
        $this->model = $model;
        $this->renderer = $renderer;
    }

    public function __invoke(Request $request, Response $response, $args): Response
    {
        try {
            $id = $this->model->updateFilm($args['id'], $request->getParsedBody());

            if(empty($id))
            {
                return $this->respondWithJson($response, ['message' => 'Film not found.'], 404);
            }
            return $this->respondWithJson($response, ['message' => 'Film updated successfully.'], 200);

        } catch (PDOException $e) {
            return $this->respondWithJson($response, ['error' => $e->getMessage()], 500);
        }
    }
}