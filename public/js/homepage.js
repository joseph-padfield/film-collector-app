const addFilmButton = document.getElementById('add-film-button')
const searchFilmsSubmit = document.getElementById('search-films-submit')
const searchFilmsInput = document.getElementById('search-films-input')

addFilmButton.addEventListener('click', function(){
    console.log('clicked');
})

searchFilmsSubmit.addEventListener('click', async function(event){
    event.preventDefault()
    console.log(searchFilmsInput.value)
    let response = await fetch(`http://0.0.0.0:8080/searchFilm/` + searchFilmsInput.value)
    let result = await response.json()
    console.log(result)
})

