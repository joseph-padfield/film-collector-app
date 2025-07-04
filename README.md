## (WORK IN PROGRESS) Film Collector API Documentation

This Film Collector App is a full-stack web application designed to help users manage and organise their personal film libraries. Built with a PHP backend using the Slim framework, it integrates the TMDB API to fetch and display rich film data, while securely storing user collections in a structured SQL database. The backend architecture follows modular design principles, leveraging dependency injection for clean, maintainable code across logging, database access, and data models. Core security best practices - including strict typing, CORS policy enforcement, and input validation - have been implemented to safeguard endpoints and enhance the overall resilience of the application.

---

## Key features:

- Search for films by title
- Add films to collection
- Update film details
- Delete films from collection
- View collection
- The app is designed to be simple and easy to use, while providing the essential features for managing a film collection.

---

### Environment variables

The following variables need to be set in a .env file. A bearer token from TMDB is needed.

DB_HOST  
DB_NAME  
DB_USER  
DB_PASSWORD  
TMDB_BEARER_TOKEN

---

### Base URL

```
http://0.0.0.0:8080
```

### Endpoints

#### Get Films

* **URL:** `/films[/{sortBy}[/{sortOrder}]]`
* **Method:** `GET`
* **Description:** Retrieves all films from the database.
* **Optional Parameters:**
  * `sortBy`: The field to sort by (e.g., title, year, director, date_watched, rating). Defaults to `title`.
  * `sortOrder`: The sort order (`ASC` or `DESC`). Defaults to `ASC`.
* **Response:**
  * 200 OK: An array of film objects.
  * 204 No Content: If no films are found.
  * 400 Bad Request: If invalid sort parameters are provided.
* **Example Request:**
```
GET http://0.0.0.0:8080/films
```
* **Example Response:**
```json
[
    {
        "id": 1,
        "title": "The Shawshank Redemption",
        "year": 1994,
        "director": "Frank Darabont",
        "studio": "Columbia Pictures",
        "image_url": "https://example.com/shawshank.jpg",
        "date_watched": 20230101,
        "rating": 5,
        "certification": "15",
        "runtime": 142,
        "actors": "Tim Robbins, Morgan Freeman",
        "review_links": "https://example.com/shawshank_review"
    },
    {
        "id": 2,
        "title": "The Dark Knight",
        "year": 2008,
        "director": "Christopher Nolan",
        "studio": "Warner Bros.",
        "image_url": "https://example.com/darkknight.jpg",
        "date_watched": 20230105,
        "rating": 4,
        "certification": "12A",
        "runtime": 152,
        "actors": "Christian Bale, Heath Ledger",
        "review_links": "https://example.com/darkknight_review"
    }
]
```

#### Add Film

* **URL:** `/films`
* **Method:** `POST`
* **Description:** Adds a new film to the database.
* **Request Body:**
```json
{
    "title": "The Godfather",
    "year": 1972,
    "director": "Francis Ford Coppola",
    "studio": "Paramount Pictures",
    "image_url": "https://example.com/godfather.jpg",
    "date_watched": 20230110,
    "rating": 5,
    "certification": "18",
    "runtime": 175,
    "actors": "Marlon Brando, Al Pacino",
    "review_links": "https://example.com/godfather_review"
}
```
* **Response:**
  * 303 See Other: If the film is added successfully.
  * 500 Internal Server Error: If there is an error adding the film.
* **Example Request:**
```
POST http://0.0.0.0:8080/films
```

#### Update Film

* **URL:** `/films/{id}`
* **Method:** `PUT`
* **Description:** Updates an existing film in the database.
* **Request Parameters:**
  * `id`: The ID of the film to update.
* **Request Body:**
```json
{
    "date_watched": 20230115,
    "rating": 4
}
```
* **Response:**
  * 200 OK: If the film is updated successfully.
  * 404 Not Found: If the film is not found.
  * 500 Internal Server Error: If there is an error updating the film.
* **Example Request:**
```
PUT http://0.0.0.0:8080/films/1
```

#### Delete Film

* **URL:** `/films/{id}`
* **Method:** `DELETE`
* **Description:** Deletes a film from the database.
* **Request Parameters:**
  * `id`: The ID of the film to delete.
* **Response:**
  * 200 OK: If the film is deleted successfully.
  * 404 Not Found: If the film is not found.
  * 500 Internal Server Error: If there is an error deleting the film.
* **Example Request:**
```
DELETE http://0.0.0.0:8080/films/1
```

#### Search Film Title (TMDB)

* **URL:** `/searchFilm/{userInput}[/{page}[/{sortBy}]]`
* **Method:** `GET`
* **Description:** Search for a film title using the TMDB API.
* **Request Parameters:**
  * `userInput`: The search query.
  * `page`: The page number of results to retrieve (optional, defaults to 1).
  * `sortBy`: The field to sort by (optional, defaults to `popularityDesc`).
    * Allowed values: `popularityAsc`, `popularityDesc`, `releasedAsc`, `releasedDesc`, `original_titleAsc`, `original_titleDesc`, `original_languageAsc`, `original_languageDesc`
* **Response:**
  * 200 OK: An array of film objects from TMDB.
  * 400 Bad Request: If invalid sort parameters are provided.
* **Example Request:**
```
GET http://0.0.0.0:8080/searchFilm/The%20Godfather
```

#### Get Film by ID (TMDB)

* **URL:** `/searchFilmId/{id}`
* **Method:** `GET`
* **Description:** Retrieve film details by ID using the TMDB API.
* **Request Parameters:**
  * `id`: The TMDB ID of the film.
* **Response:**
  * 200 OK: A film object from TMDB.
* **Example Request:**
```
GET http://0.0.0.0:8080/searchFilmId/238
```

### Error Handling

The API uses standard HTTP status codes to indicate success or failure of requests. In case of errors, the response body may contain additional information about the error.

### Authentication and Authorization

Currently, the API does not implement authentication or authorization. All endpoints are publicly accessible.

