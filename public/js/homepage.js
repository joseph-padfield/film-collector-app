const API_BASE_URL = 'http://0.0.0.0:8080' //development url. change in production
const IMAGE_BASE_URL = 'https://image.tmdb.org/t/p/w200'

const addFilmButton = document.getElementById('add-film-button')
const searchFilmModal = document.getElementById('search-films-modal')
const searchFilmsSubmit = document.getElementById('search-films-submit')
const searchFilmsInput = document.getElementById('search-films-input')
const searchResultsContainer = document.getElementById('search-results-container')
const closeSearchFilmsModal = document.getElementById('close-search-films-modal')
const paginationAnchor = document.getElementById('pagination-anchor')
const filmContainer = document.getElementById('collection-display')
const deleteFilmButtons = document.querySelectorAll('.delete-film')

//expand/collapse collection film details
document.addEventListener('DOMContentLoaded', () => {
    filmContainer.addEventListener('click', (event) => {
        const clickedFilm = event.target.closest('.film')

        if (clickedFilm) {
            const allFilms = filmContainer.querySelectorAll('.film.expanded')
            allFilms.forEach(film => {
                if (film !== clickedFilm) {
                    film.classList.remove('expanded')
                }
            })
            clickedFilm.classList.toggle('expanded')
        }
    })
})


addFilmButton.addEventListener('click', openSearchModal)
closeSearchFilmsModal.addEventListener('click', closeSearchModal)
searchFilmsSubmit.addEventListener('click', (event) => searchFilms(searchFilmsInput.value, 1, event))
deleteFilmButtons.forEach(button => {
    button.addEventListener('click', (e) => {
        e.preventDefault()
        const filmElement = e.target.closest('.film')
        const filmId = filmElement.dataset.filmId
        deleteItem(filmId)
    })
})

//opens modal, clears previous results, prevents scrolling on the body
function openSearchModal(e) {
    e.preventDefault()
    clearSearchResults(true)
    searchFilmModal.showModal()
    document.body.style.overflow = 'hidden' //preventing background scrolling
}

//close modal, restore body scrolling
function closeSearchModal(e) {
    e.preventDefault()
    searchFilmModal.close()
    document.body.style.overflow = 'auto' //restoring body scrolling
    clearSearchResults(true) //clearing search results AND input field
}

//submit search - tmdb - title. optional argument for pagination
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

//fetch film data from api
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

//render the results
function renderSearchResults(searchResults) {
    const {results, total_pages, page} = searchResults
    if (total_pages > 1) {
        renderPagination(page, total_pages)
    }
    results.forEach(renderFilm)
}

//render pagination controls
function renderPagination(currentPage, totalPages) {
    paginationAnchor.innerHTML = ''
    const paginationContainer = document.createElement('div')
    paginationContainer.classList.add('pagination-container')
    let paginationHtml = ''
    //option to page back if on page > 1
    if (currentPage > 1) {
        paginationHtml += '<button class="prev-page">Previous</button>'
    }
    //display current page and count
    paginationHtml += `<span class="page-display">Page ${currentPage} of ${totalPages}</span>`
    //option for next page if current page < total pages
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

//render single film result
function renderFilm(result) {
    if (!result.poster_path) return //don't show item if missing poster
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

//shows more detailed version of film
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
            filmDetails.scrollTop = 0 //reset scroll position
        } catch (error) {
            console.error("Error in showFilmDetails:", error)
        }
    })
}

//fetch detailed film info from api
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

//render detailed film info
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
                <p>Runtime: ${runtime > 0 ? runtime : '?'}</p>
                <p>Director: ${director ? JSON.parse(director).join(', ') : ''}</p>
                <p>Overview: ${overview}</p>
                <p class="cast-list">Cast: ${cast ? JSON.parse(cast).join(', ') : ''}</p>
            </div>
        </div>
    `
    addToDb(dbData)
    const closeModal = filmDetails.querySelector('#close-film-details')
    if (closeModal) {
        closeModal.addEventListener('click', function closeHandler(e) {
            e.preventDefault()
            e.stopPropagation()
            filmDetails.close()
            closeModal.removeEventListener('click', closeHandler)
        })
    }
}

//adding film to db
async function addToDb(movieDetails) {
    const submit = document.getElementById('submit-add-to-db')
    if (!submit) return //handles cases where no submit button
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
            window.location.reload() //reload page.
        } catch (error) {
            console.error("Fetch error:", error)
        }
    })
}

//close film detail view
function closeSearchDetails(filmDetails) {
    const closeModal = document.getElementById('close-film-details')
    const closeHandler = (e) => {  //store handler function so it can be removed, rather than an anonymous function
        e.preventDefault()
        e.stopPropagation() //stop the click from propagating to the parent element. without this it will simply reopen
        filmDetails.close()
        closeModal.removeEventListener('click', closeHandler) //remove listener
    }
    closeModal.addEventListener('click', closeHandler)
}

//clear search results with option for input field clear
function clearSearchResults(clearInput) {
    searchResultsContainer.innerHTML = ''
    paginationAnchor.innerHTML = ''
    if (clearInput === true) {
        searchFilmsInput.value = ''
    }
}

//delete item from db
async function deleteItem(id) {
    try {
        const response = await fetch(`${API_BASE_URL}/films/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        if (!response.ok) {
            const errorData = await response.json()
            console.error(errorData)
        }
        window.location.reload()
    } catch (error) {
        console.error("Error deleting film with error:", error)
    }
}