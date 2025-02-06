<?php

namespace App\Models;

use App\Interfaces\TMDBModelInterface;
use GuzzleHttp\Client;
use Psr\Container\ContainerInterface;

class TMDBModel implements TMDBModelInterface
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function searchFilmTitle($userInput, $page=1)
    {
        $client = new Client();
        $settings = $this->container->get('settings');
        $tmdbToken = $settings['tmdb']['bearer_token'];

        $response = $client->request('GET', 'https://api.themoviedb.org/3/search/movie?query=' . $userInput . '&page=' . $page, [
            'headers' => [
                'Authorization' => 'Bearer ' . $tmdbToken,
                'accept' => 'application/json',
            ],
        ]);

        return $response->getBody();
    }

    public function searchFilmId($id)
    {
        $client = new Client();
        $settings = $this->container->get('settings');
        $tmdbToken = $settings['tmdb']['bearer_token'];

        $response = $client->request('GET', 'https://api.themoviedb.org/3/movie/' . $id, [
            'headers' => [
                'Authorization' => 'Bearer ' . $tmdbToken,
                'accept' => 'application/json',
            ],
        ]);

        $response2 = $client->request('GET', 'https://api.themoviedb.org/3/movie/' . $id . '/credits', [
            'headers' => [
                'Authorization' => 'Bearer ' . $tmdbToken,
                'accept' => 'application/json',
            ]
        ]);

        $responseData= json_decode($response->getBody(), true);
        $response2Data= json_decode($response2->getBody(), true);

        $combinedResponseData = [
            $responseData, $response2Data,
        ];

        return json_encode($combinedResponseData);
    }
}