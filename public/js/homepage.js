// const addFilmButton = document.getElementById('add-film-button')
// const searchFilmsSubmit = document.getElementById('search-films-submit')
// const searchFilmsInput = document.getElementById('search-films-input')
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
