<?php

namespace App\Models;

use GuzzleHttp\Client;

class TMDBModel
{
    public function searchFilmTitle($userInput, $page=1)
    {
        $client = new Client();

        $response = $client->request('GET', 'https://api.themoviedb.org/3/search/movie?query=' . $userInput . '&page=' . $page, [
            'headers' => [
                'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI1OGEwYWI0MjYxODZkNjgxMDY5YTdkOTIxZmFiZWU3ZSIsIm5iZiI6MTczMzEzNjU2My4yMDQsInN1YiI6IjY3NGQ5MGIzZTdkNmY4MjkyZTBiOGZjNiIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.dWzkHKvEIR4192_L3d10rXcnIImGs0h2_0NoFjfddyI',
                'accept' => 'application/json',
            ],
        ]);

        return $response->getBody();
    }

    public function searchFilmId($id)
    {
        $client = new Client();

        $response = $client->request('GET', 'https://api.themoviedb.org/3/movie/' . $id, [
            'headers' => [
                'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI1OGEwYWI0MjYxODZkNjgxMDY5YTdkOTIxZmFiZWU3ZSIsIm5iZiI6MTczMzEzNjU2My4yMDQsInN1YiI6IjY3NGQ5MGIzZTdkNmY4MjkyZTBiOGZjNiIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.dWzkHKvEIR4192_L3d10rXcnIImGs0h2_0NoFjfddyI',
                'accept' => 'application/json',
            ],
        ]);

        $response2 = $client->request('GET', 'https://api.themoviedb.org/3/movie/' . $id . '/credits', [
            'headers' => [
                'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI1OGEwYWI0MjYxODZkNjgxMDY5YTdkOTIxZmFiZWU3ZSIsIm5iZiI6MTczMzEzNjU2My4yMDQsInN1YiI6IjY3NGQ5MGIzZTdkNmY4MjkyZTBiOGZjNiIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.dWzkHKvEIR4192_L3d10rXcnIImGs0h2_0NoFjfddyI',
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