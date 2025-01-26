<?php

namespace App\Controllers;

use App\Abstracts\Controller;
use App\Models\TMDBModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TMDBGetFilmByIdController extends Controller
{
    private TMDBModel $model;

    public function __construct(TMDBModel $model)
    {
        $this->model = $model;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $id = $args['id'];
        $apiResponse = $this->model->searchFilmId($id);

        $data = json_decode($apiResponse, true);

        [
            'title' => $title,
            'release_date' => $release_date,
            'poster_path' => $poster_path,
            'tagline' => $tagline,
            'overview' => $overview,
            'original_language' => $original_language,
            'production_countries' => $production_countries,
            'production_companies' => $production_companies,
            'runtime' => $runtime,
        ] = $data[0];

        $director = [];
        //extract director
        foreach ($data[1]['crew'] as $crew) {
            if ($crew['job'] == 'Director') {
                $director[] = $crew['name'];
            }
        }

        $castList = [];
        //extract actors
        foreach ($data[1]['cast'] as $cast) {
            $castList[] = $cast['name'];
            $castListString = serialize($castList);
        }

        $infoToPass =
            [
                'title' => $title,
                'release_date' => $release_date,
                //poster path prepended by url. w500 is width and can be changed
                'poster_path' => 'https://image.tmdb.org/t/p/w500' . $poster_path,
                'tagline' => $tagline,
                'overview' => $overview,
                'original_language' => $original_language,
                'production_countries' => $production_countries,
                'director' => $director,
                'production_companies' => $production_companies,
                'runtime' => $runtime,
                'cast' => $castListString,
            ];

        $response->getBody()->write(json_encode($infoToPass));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}