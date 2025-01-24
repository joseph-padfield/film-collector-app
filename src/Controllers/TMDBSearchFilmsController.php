<?php

namespace App\Controllers;

use App\Abstracts\Controller;
use App\Models\TMDBModel;
use DateTime;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TMDBSearchFilmsController extends Controller
{
    private TMDBModel $model;

    public function __construct(TMDBModel $model)
    {
        $this->model = $model;
    }

    public function __invoke(Request $request, Response $response, array $args)
    {
        $userInput = urlencode($args['userInput']);
        $page = $args['page'] ?? 1;
        $apiResponse = $this->model->searchFilmTitle($userInput, $page);

        $data = json_decode($apiResponse, true);

        //make default sort popularity descending

        if (!isset($args['sortBy']))
        {
            $args['sortBy'] = 'popularityDesc';
        }

        if (isset($args['sortBy'])) {
            $sortBy = $args['sortBy'];

            if ($sortBy === 'popularityAsc') {
                usort($data['results'], function ($a, $b) {
                    return $a['popularity'] <=> $b['popularity'];
                });
            } elseif ($sortBy === 'popularityDesc') {
                usort($data['results'], function ($a, $b) {
                    return $b['popularity'] <=> $a['popularity'];
                });
            } elseif ($sortBy === 'releasedAsc') {
                usort($data['results'], function ($a, $b) {
                    $dateA = new DateTime($a['release_date']);
                    $dateB = new DateTime($b['release_date']);
                    return $dateA <=> $dateB;
                });
            } elseif ($sortBy === 'releasedDesc') {
                usort($data['results'], function ($a, $b) {
                    $dateA = new DateTime($a['release_date']);
                    $dateB = new DateTime($b['release_date']);
                    return $dateB <=> $dateA;
                });
            } elseif ($sortBy === 'original_titleAsc') {
                usort($data['results'], function ($a, $b) {
                    return strcmp($a['original_title'], $b['original_title']);
                });
            } elseif ($sortBy === 'original_titleDesc') {
                usort($data['results'], function ($a, $b) {
                    return strcmp($b['original_title'], $a['original_title']);
                });
            } elseif ($sortBy === 'original_languageAsc') {
                usort($data['results'], function ($a, $b) {
                    return strcmp($a['original_language'], $b['original_language']);
                });
            } elseif ($sortBy === 'original_languageDesc') {
                usort($data['results'], function ($a, $b) {
                    return strcmp($b['original_language'], $a['original_language']);
                });
            } else {
                $errorMessage = [
                    'error: ' => 'Unknown sort by: ' . $sortBy
                    ];

                $response->getBody()->write(json_encode($errorMessage));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(400);
            }

            $response->getBody()->write(json_encode($data));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        } else {
            $response->getBody()->write(json_encode($data));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        }
    }
}