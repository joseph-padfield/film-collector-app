const API_BASE_URL = 'http://0.0.0.0:8080'
const IMAGE_BASE_URL = 'https://image.tmdb.org/t/p/w200'

const addFilmButton = document.getElementById('add-film-button')
const searchFilmModal = document.getElementById('search-films-modal')
const searchFilmsSubmit = document.getElementById('search-films-submit')
const searchFilmsInput = document.getElementById('search-films-input')
const searchResultsContainer = document.getElementById('search-results-container')
const closeSearchFilmsModal = document.getElementById('close-search-films-modal')
const paginationAnchor = document.getElementById('pagination-anchor')
const filmContainer = document.getElementById('collection-display')

document.addEventListener('DOMContentLoaded', () => {
    filmContainer.addEventListener('click', (event) => {  // Use 'event' here
        const clickedFilm = event.target.closest('.film'); // Find the clicked film element

        if (clickedFilm) {
            const allFilms = filmContainer.querySelectorAll('.film.expanded')
            allFilms.forEach(film => {
                if (film !== clickedFilm) {
                    film.classList.remove('expanded')
                }
            })
            clickedFilm.classList.toggle('expanded'); // Toggle the 'expanded' class
        }
    });
});

addFilmButton.addEventListener('click', openSearchModal)
closeSearchFilmsModal.addEventListener('click', closeSearchModal)
searchFilmsSubmit.addEventListener('click', (event) => searchFilms(searchFilmsInput.value, 1, event))


function openSearchModal(e) {
    e.preventDefault()
    clearSearchResults(true)
    searchFilmModal.showModal()
    document.body.style.overflow = 'hidden'
}

function closeSearchModal(e) {
    e.preventDefault()
    searchFilmModal.close()
    document.body.style.overflow = 'auto'
    clearSearchResults(true)
}

async function searchFilms(searchTerm, page = 1, event) {
    if (event) {
        event.preventDefault()
    }
    clearSearchResults()

    let searchResults = await fetchFilmData(searchTerm, page)

    if (!searchResults) {
        console.error("Error fetching film data.")
        return
    }

    renderSearchResults(searchResults)
}

async function fetchFilmData(searchTerm, page = 1) {
    try {
        const response = await fetch(`${API_BASE_URL}/searchFilm?title=${searchTerm}&page=${page}`)
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`)
        }
        return await response.json()
    } catch (error) {
        console.error("Error fetching film data:", error)
        return null
    }
}

function renderSearchResults(searchResults) {
    const {results, total_pages, page} = searchResults

    if (total_pages > 1) {
        renderPagination(page, total_pages)
    }

    results.forEach(renderFilm)
}

function renderPagination(currentPage, totalPages) {
    const paginationContainer = document.createElement('div')
    paginationContainer.classList.add('pagination-container')

    let paginationHtml = ''

    if (currentPage > 1) {
        paginationHtml += '<button class="prev-page">Previous</button>'
    }

    paginationHtml += `<span class="page-display">Page ${currentPage} of ${totalPages}</span>`

    if (currentPage < totalPages) {
        paginationHtml += '<button class="next-page">Next</button>'
    }

    paginationContainer.innerHTML = paginationHtml
    paginationAnchor.appendChild(paginationContainer)

    if (currentPage > 1) {
        const prevButton = paginationContainer.querySelector('.prev-page')
        prevButton.addEventListener('click', () => searchFilms(searchFilmsInput.value, currentPage - 1, event))
    }

    if (currentPage < totalPages) {
        const nextButton = paginationContainer.querySelector('.next-page')
        nextButton.addEventListener('click', (event) => searchFilms(searchFilmsInput.value, currentPage + 1, event))
    }

}

function renderFilm(result) {
    if (!result.poster_path) return

    const filmBox = document.createElement('div')
    filmBox.classList.add('film-box')

    const posterImage = document.createElement('img')
    posterImage.src = `${IMAGE_BASE_URL}${result.poster_path}`
    posterImage.alt = result.original_title
    posterImage.classList.add('poster-image')

    const filmTitle = document.createElement('h3')
    filmTitle.textContent = `${result.title} (${result.release_date ? result.release_date.slice(0, 4) : ''})`
    filmTitle.classList.add('film-title')

    filmBox.append(posterImage, filmTitle)
    searchResultsContainer.append(filmBox)

    showFilmDetails(filmBox, result)
}

async function showFilmDetails(filmBox, result) {
    const filmDetails = document.createElement('dialog')
    filmDetails.classList.add('search-film-detailed-view')
    filmBox.append(filmDetails)

    filmBox.addEventListener('click', async (e) => {
        e.preventDefault()

        try {
            const movieDetails = await fetchFilmDetailsData(result.id)
            if (!movieDetails) {
                console.error("Error fetching movie details.")
                return
            }
            renderMovieDetails(filmDetails, movieDetails)
            filmDetails.showModal()
            filmDetails.scrollTop = 0

        } catch (error) {
            console.error("Error in showFilmDetails:", error)
        }
    })
}

async function fetchFilmDetailsData(movieId) {
    try {
        const response = await fetch(`${API_BASE_URL}/searchFilm/${movieId}`)
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`)
        }
        return await response.json()
    } catch (error) {
        console.error("Error fetching film details data:", error)
        return null
    }
}

function renderMovieDetails(filmDetails, dbData) {
    const {title, release_date, runtime, director, overview, cast, poster_path} = dbData

    filmDetails.innerHTML = `
        <div class="film-details-container">
            <div class="film-details-poster">
                <button id="close-film-details"><i class="fa fa-times-circle-o" aria-hidden="true"></i></button>
                <img src="${IMAGE_BASE_URL}${poster_path}" alt="${title} poster" class="film-details-poster-image" />
            </div>
            <div class="film-details-text">
                <button id="submit-add-to-db">Add to collection</button>
                <p>Year: ${release_date ? release_date.slice(0, 4) : ''}</p>
                <p>Runtime: ${runtime}</p>
                <p>Director: ${director ? JSON.parse(director).join(', ') : ''}</p>
                <p>Overview: ${overview}</p>
                <p class="cast-list">Cast: ${cast ? JSON.parse(cast).join(', ') : ''}</p>
            </div>
        </div>
    `

    addToDb(dbData)

    const closeModal = filmDetails.querySelector('#close-film-details');
    if (closeModal) {
        closeModal.addEventListener('click', function closeHandler(e) {
            e.preventDefault();
            e.stopPropagation();
            filmDetails.close();
            closeModal.removeEventListener('click', closeHandler); // Remove the listener
        });
    }
}


async function addToDb(movieDetails) {
    const submit = document.getElementById('submit-add-to-db')
    if (!submit) return

    submit.addEventListener('click', async (e) => {
        e.preventDefault()

        try {
            const response = await fetch(`${API_BASE_URL}/films`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(movieDetails)
            })

            if (!response.ok) {
                const errorData = await response.json()
                console.error("Error adding film:", errorData)
                return
            }

            console.log('SUCCESS: ', await response.json())
            window.location.reload()

        } catch (error) {
            console.error("Fetch error:", error)
        }
    })
}

function closeSearchDetails(filmDetails) {
    const closeModal = document.getElementById('close-film-details')

    const closeHandler = (e) => {  // Store the handler function
        e.preventDefault()
        e.stopPropagation()
        filmDetails.close()
        closeModal.removeEventListener('click', closeHandler) // Remove the listener
    }

    closeModal.addEventListener('click', closeHandler)
}

function clearSearchResults(clearInput) {
    searchResultsContainer.innerHTML = ''
    paginationAnchor.innerHTML = ''
    if (clearInput === true) {
        searchFilmsInput.value = ''
    }
}