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
})

searchFilmsSubmit.addEventListener('click', async function(event){
    event.preventDefault()
    let response = await fetch(`http://0.0.0.0:8080/searchFilm/` + searchFilmsInput.value)
    let searchResults = await response.json()
    searchResults.results.forEach((result) => {
        const filmBox = document.createElement('div')
        filmBox.classList.add('film-box')
        const posterImage = document.createElement("img")
        posterImage.src = 'https://image.tmdb.org/t/p/w500' + result.poster_path
        posterImage.alt = result.original_title
        posterImage.classList.add('poster-image')
        const filmTitle = document.createElement("h3")
        filmTitle.innerText = result.original_title
        filmTitle.classList.add('film-title')
        searchResultsContainer.append(filmBox)
        filmBox.append(posterImage)
        filmBox.append(filmTitle)
        showFilmDetails(filmBox, result)
    })
//     if there are more pages of results, i.e. if pages > 1, then show a 'see more' option
})

// now to select a film to view its contents - try hover - make a function and put it into the event listener above
const showFilmDetails = (filmBox, result) => {
    const filmDetails = document.createElement('div')
    filmBox.append(filmDetails)
    filmBox.addEventListener('click', async (e) => {
        e.preventDefault()

        if (filmDetails.innerHTML.length > 0) {
            filmDetails.innerHTML = ''
        }
        else {
            // async call get movie by id from tmdb, show data here also button to add to library
            let response = await fetch(`http://0.0.0.0:8080/searchFilmId/` + result.id)
            let movieDetails = await response.json()
            console.log(movieDetails)
            filmDetails.innerHTML = '' +
                '<h3>Year: ' + movieDetails.release_date.slice(0,4) + '</h3>' +
                '<h3>Director: ' + JSON.parse(movieDetails.director).join(', ') + '</h3>' +
                '<h3>Overview: ' + movieDetails.overview + '</h3>' +
                '<h3>Director: ' + JSON.parse(movieDetails.director).join(', ') + '</h3>' +
                '<h3>Director: ' + JSON.parse(movieDetails.director).join(', ') + '</h3>' +
                '<h3>Director: ' + JSON.parse(movieDetails.director).join(', ') + '</h3>' +
                '<h3>Director: ' + JSON.parse(movieDetails.director).join(', ') + '</h3>' +
                '' +
                '' +
                '' +
                '' +
                '' +
                ''
            // filmDetails.innerHTML = '<h3>Cast: ' + JSON.parse(movieDetails.cast).join(', ') + '</h3>'
            // filmDetails.innerHTML = '<h3>Cast: ' + JSON.parse(movieDetails.cast).join(', ') + '</h3>'
            // filmDetails.innerHTML = '<h3>Cast: ' + JSON.parse(movieDetails.cast).join(', ') + '</h3>'
        }
    })
}






// const searchResultsContainer = document.getElementById('search-results-container')
//
//
//
// addFilmButton.addEventListener('click', function(){
//     console.log('clicked');
// })
//
// searchFilmsSubmit.addEventListener('click', async function(event){
//     event.preventDefault()
//     let response = await fetch(`http://0.0.0.0:8080/searchFilm/` + searchFilmsInput.value)
//     let searchResults = await response.json()
//     searchResults.results.forEach((result) => {
//         const posterImage = document.createElement("img")
//         posterImage.src = 'https://image.tmdb.org/t/p/w500' + result.poster_path
//         posterImage.alt = result.original_title
//         posterImage.classList.add('poster-image')
//         searchResultsContainer.append(posterImage)
//     })
// })
//




