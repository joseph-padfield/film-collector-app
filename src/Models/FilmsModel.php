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
        $sql = 'INSERT INTO films (title, year, director, studio, image_url, date_watched, rating, certification, runtime, actors, review_links) VALUES (:title, :year, :director, :studio, :image_url, :date_watched, :rating, :certification, :runtime, :actors, :review_links)';

        try {
            $query = $this->db->prepare($sql);
            $query->bindValue(':title', $newFilm['title'], PDO::PARAM_STR);
            $query->bindValue(':year', $newFilm['year'], PDO::PARAM_INT);
            $query->bindValue(':director', $newFilm['director'], PDO::PARAM_STR);
            $query->bindValue(':studio', $newFilm['studio'], PDO::PARAM_STR);
            $query->bindValue(':image_url', $newFilm['image_url'], PDO::PARAM_STR);
            $query->bindValue(':date_watched', $newFilm['date_watched'], PDO::PARAM_INT);
            $query->bindValue(':rating', $newFilm['rating'], PDO::PARAM_INT);
            $query->bindValue(':certification', $newFilm['certification'], PDO::PARAM_STR);
            $query->bindValue(':runtime', $newFilm['runtime'], PDO::PARAM_INT);
            $query->bindValue(':actors', $newFilm['actors'], PDO::PARAM_STR);
            $query->bindValue(':review_links', $newFilm['review_links'], PDO::PARAM_STR);

            $query->execute();

            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error adding new film: " . $e->getMessage());
            return false;
        }
    }

    public function getFilms(): array
    {
        $sql = 'SELECT * FROM films';
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