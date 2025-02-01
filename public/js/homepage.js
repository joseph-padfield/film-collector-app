const addFilmButton = document.getElementById('add-film-button')
const searchFilmModal = document.getElementById('search-films-modal');
const searchFilmsSubmit = document.getElementById('search-films-submit')
const searchFilmsInput = document.getElementById('search-films-input')
const searchResultsContainer = document.getElementById('search-results-container')
const closeSearchFilmsModal = document.getElementById('close-search-films-modal')

addFilmButton.addEventListener('click', (e) => {
    e.preventDefault()
    searchResultsContainer.innerHTML = ''
    searchFilmModal.showModal()
})

closeSearchFilmsModal.addEventListener('click', (e) => {
    e.preventDefault()
    searchFilmModal.close()
    searchResultsContainer.innerHTML = ''
    searchFilmsInput.value = ''
})

searchFilmsSubmit.addEventListener('click', async function (e) {
    e.preventDefault()
    let searchResults = null
    let response = await fetch(`http://0.0.0.0:8080/searchFilm?title=` + searchFilmsInput.value + '&page=')
    searchResults = await response.json()
    const pages = searchResults.total_pages
    searchResultsContainer.innerHTML = ''
    if (pages > 1 && searchResults.page > 1) {
        const backPage = document.createElement('div')
        backPage.innerHTML = `<button id="previous-page">Previous</button>`
        backPage.addEventListener('click', (e) => {
            //page -1
        })
        searchResultsContainer.append(backPage)
    }
    searchResults.results.forEach((result) => {
        if (result.poster_path === null || result.release_date === "") {
            return
        }
        const filmBox = document.createElement('div')
        filmBox.classList.add('film-box')

        const posterImage = document.createElement("img")
        posterImage.src = 'https://image.tmdb.org/t/p/w200' + result.poster_path
        posterImage.alt = result.original_title
        posterImage.classList.add('poster-image')

        const filmTitle = document.createElement("h3")
        filmTitle.innerText = result.title + ' (' + result.release_date.slice(0, 4) + ')'
        filmTitle.classList.add('film-title')

        searchResultsContainer.append(filmBox)

        filmBox.append(posterImage)
        filmBox.append(filmTitle)

        showFilmDetails(filmBox, result)
    })
    if (pages > 1 && searchResults.page < pages) {
        const nextPage = document.createElement('div')
        nextPage.innerHTML = `<button id="next-page">Next</button>`
        nextPage.addEventListener('click', (e) => {
            //page + 1
        })
        searchResultsContainer.appendChild(nextPage)
    }
})

// WHEN YOU SHOW DETAILS FOR A FILM, DISABLE THAT FUNCTIONALITY FOR THE OTHER SEARCH RESULTS. SO YOU HAVE TO EXIT THE
// DETAILED FILM VIEW OF THE CURRENT FILM IN ORDER TO VIEW THAT OF ANOTHER. THIS WILL HOPEFULLY SOLVE A PROBLEM
// THAT WE HAVE BEEN STUCK ON FOR A WHILE.
const showFilmDetails = (filmBox, result) => {
    const filmDetails = document.createElement('div')
    filmBox.append(filmDetails)
    filmBox.addEventListener('click', async (e) => {
        e.preventDefault()
        //if info for another film is already showing, this needs to be hidden
        if (filmDetails.innerHTML.length > 0) {
            filmDetails.innerHTML = ''
        } else {
            // async call get movie by id from TMDB, show data here also button to add to library
            let response = await fetch(`http://0.0.0.0:8080/searchFilm/` + result.id)
            let movieDetails = await response.json()

            filmDetails.innerHTML =
                '<p>Year: ' + movieDetails.release_date.slice(0, 4) + '</p>' +
                '<p>Runtime: ' + movieDetails.runtime + '</p>' +
                '<p>Director: ' + JSON.parse(movieDetails.director).join(', ') + '</p>' +
                '<p>Overview: ' + movieDetails.overview + '</p>' +
                '<p>Cast: ' + JSON.parse(movieDetails.cast).join(', ') + '</p>' +
                '<button id="submit-add-to-db">Add to collection</button>'

            addToDb(movieDetails)
            //     event listener to add to DB
        }
    })
}

const addToDb = async (movieDetails) => {
    const submit = document.getElementById('submit-add-to-db');

    submit.addEventListener('click', async (e) => {
        e.preventDefault();

        try {
            let response = await fetch(`http://0.0.0.0:8080/films`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(movieDetails)
            });

            if (!response.status === 201) {
                // Handle errors (e.g., display an error message)
                const errorData = await response.json();
                console.error("Error adding film:", errorData);
            } else {
                const data = await response.json();
                console.log('SUCCESS: ', data);
                // could change the reload later using  AJAX to just update teh collection display html, rather than reloading the whole page. Not a huge deal, though.
                window.location.reload();

            }
        } catch (error) {
            console.error("Fetch error:", error);
        }
    });
};
