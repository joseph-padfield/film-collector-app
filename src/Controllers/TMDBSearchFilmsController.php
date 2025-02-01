<?php

namespace App\Controllers;

use App\Abstracts\Controller;
use App\Interfaces\TMDBModelInterface;
use DateTime;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TMDBSearchFilmsController extends Controller
{
    private TMDBModelInterface $model;

    public function __construct(TMDBModelInterface $model)
    {
        $this->model = $model;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {

        $title = trim($request->getQueryParams()['title']);

        if (empty($title)) {
            return $this->respondWithJson($response, [
                'status' => 'Error',
                'message' => 'Validation failed',
                'errors' => ['User input cannot be empty']
            ], 400);
        }

        $title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

        if (strlen($title) > 255) {
            return $this->respondWithJson($response, [
                'status' => 'Error',
                'message' => 'Validation failed',
                'errors' => ['User input cannot be longer than 255 characters']
            ], 400);
        }

        $page = $request->getQueryParams()['page'];

        if (!empty($page)) {
            if (!ctype_digit($page)) //ctype is less strict than is_int, but not so broad as to include floats, which would be the case with is_numeric
            {
                return $this->respondWithJson($response, [
                    'status' => 'Error',
                    'message' => 'Validation failed',
                    'errors' => ['Page must be an integer']
                ], 400);
            }
        } else {
            $page = 1;
        }

        $apiResponse = $this->model->searchFilmTitle($title, $page);

        $data = json_decode($apiResponse, true);

        $response->getBody()->write(json_encode($data));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}