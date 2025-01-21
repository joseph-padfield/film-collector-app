<?php

namespace App\Controllers;

use App\Models\FilmsModel;
use App\Abstracts\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;

class AddFilmController extends Controller
{
    private FilmsModel $model;
    private PhpRenderer $renderer;

    public function __construct(FilmsModel $model, PhpRenderer $renderer)
    {
        $this->model = $model;
        $this->renderer = $renderer;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $newFilm = $request->getParsedBody();
            $insertedId = $this->model->addFilm($newFilm);

            if ($insertedId)
            {
                return $this->respondWithJson($response, ['message' => 'success'])->withStatus(303);
            }
            else
            {
                return $this->respondWithJson($response, ['message' => 'Failed to add new film'], 500);
            }
        } catch (\PDOException $e) {
            return $this->respondWithJson($response, ['message' => $e->getMessage()], 500);
        }
    }
}