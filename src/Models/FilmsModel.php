<?php

namespace App\Models;

use PDO;
use App\Factories\PDOFactory;
use PDOException;
use Psr\Container\ContainerInterface;

class FilmsModel
{
    private PDO $db;

    public function __construct(PDOFactory $db, ContainerInterface $container)
    {
        $this->db = $db($container);
    }

    public function addFilm(array $newFilm): int
    {
        $sql = 'INSERT INTO films (title, release_date, poster_path, tagline, overview, original_language, production_countries, director, production_companies, runtime, date_watched, rating, cast) VALUES (:title, :release_date, :poster_path, :tagline, :overview, :original_language, :production_countries, :director, :production_companies, :runtime, :date_watched, :rating, :cast)';

        try {

            $query = $this->db->prepare($sql);
            $query->bindValue(':title', $newFilm['title']);
            $query->bindValue(':release_date', $newFilm['release_date']);
            $query->bindValue(':poster_path', $newFilm['poster_path']);
            $query->bindValue(':tagline', $newFilm['tagline']);
            $query->bindValue(':overview', $newFilm['overview']);
            $query->bindValue(':original_language', $newFilm['original_language']);
            $query->bindValue(':production_countries', $newFilm['production_countries']);
            $query->bindValue(':director', $newFilm['director']);
            $query->bindValue(':production_companies', $newFilm['production_companies']);
            $query->bindValue(':runtime', $newFilm['runtime']);
            $query->bindValue(':date_watched', 0);
            $query->bindValue(':rating', 0);
            $query->bindValue(':cast', $newFilm['cast']);

            $query->execute();

            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error adding new film: " . $e->getMessage());
            return false;
        }
    }

    public function getFilms($sortBy, $sortOrder): array
    {
        $sql = 'SELECT * FROM films ORDER BY ' . $sortBy . ' ' . $sortOrder;

        $query = $this->db->prepare($sql);
        $query->execute();

        return $query->fetchAll();
    }

    public function updateFilm(int $filmId, array $updateInfo): bool
    {
        $sql = 'UPDATE films SET date_watched = :date_watched, rating = :rating WHERE id = :id';

        try
        {
            $query = $this->db->prepare($sql);
            $query->bindValue(':id', $filmId, PDO::PARAM_INT);
            $query->bindValue(':date_watched', $updateInfo['date_watched'], PDO::PARAM_INT);
            $query->bindValue(':rating', $updateInfo['rating'], PDO::PARAM_INT);

            $query->execute();

            return true;
        } catch (PDOException $e) {
            error_log("Error updating film: " . $e->getMessage());
            return false;
        }
    }

    public function deleteFilm(int $id): int
    {
        $sql = 'DELETE FROM films WHERE id = :id';

        try {
            $query = $this->db->prepare($sql);
            $query->bindValue(':id', $id, PDO::PARAM_INT);

            $query->execute();

            return $query->rowCount();
        } catch (PDOException $e) {
            error_log("Error deleting film: " . $e->getMessage());
            return false;
        }
    }
}