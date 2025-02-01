<?php

namespace App\Controllers;

use App\Interfaces\FilmsModelInterface;
use App\Abstracts\Controller;
use PDOException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// WORK IN PROGRESS
class UpdateFilmController extends Controller
{
    private FilmsModelInterface $model;

    public function __construct(FilmsModelInterface $model)
    {
        $this->model = $model;
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