<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Filmera Portal</title>
        <link rel="stylesheet" href="../assets/index.css">
    </head>
    <body>
        <?php include '../includes/navbar.php'; ?>
        <main>
            <?php include '../includes/header.php'; ?>

            <section class="search-movie" aria-labelledby="search-movie-heading">
                <h2 id="search-movie-heading">Search Your Movie</h2>
                <form id="movieSearchForm" class="movie-form">
                <div class="form-group">
                    <input type="text" id="searchInput" placeholder="Enter movie title..." required />
                </div>
                <button type="submit" class="btn btn-primary">Search</button>
                </form>
                <div id="searchResults" class="movie-results"></div>
            </section>

            <section class="movie-management">
                <h2>Manage Your Movies</h2>

                <form id="addMovieForm" class="movie-form">
                <h3>Add New Movie</h3>
                <div class="form-group">
                    <input type="text" id="title" placeholder="Title" required />
                </div>
                <div class="form-group">
                    <input type="text" id="genre" placeholder="Genre" required />
                </div>
                <div class="form-group">
                    <input type="text" id="director" placeholder="Director" required />
                </div>
                <div class="form-group">
                    <input type="number" id="release_year" placeholder="Release Year" required />
                </div>
                <div class="form-group">
                    <textarea id="synopsis" placeholder="Synopsis" required></textarea>
                </div>
                <div class="form-group">
                    <input type="url" id="poster_url" placeholder="Poster URL (optional)" />
                </div>
                <button type="submit" class="btn btn-primary">Add Movie</button>
                </form>

                <div id="movieList" class="movie-list"></div>
            </section>
        </main>

        <?php include '../includes/footer.php'; ?>

        <script>
            const API_URL = 'http://localhost/Filmera_api/api/films.php';
            const API_KEY = '24be7507fc7884e2718d2cbe7cc34a7a';

            function showLoading(container) {
                container.innerHTML = '<div class="loading">Loading...</div>';
            }

            function showError(container, message) {
                container.innerHTML = `<div class="error">${message}</div>`;
            }

            // get semua film
            async function fetchMovies() {
                const list = document.getElementById('movieList');
                showLoading(list);
                
                try {
                const res = await fetch(API_URL, {
                    headers: { 'Authorization': 'Bearer ' + API_KEY }
                });
                
                if (!res.ok) {
                    throw new Error('Failed to fetch movies');
                }
                
                const data = await res.json();
                renderMovies(data);
                } catch (error) {
                showError(list, error.message);
                }
            }

            // Render film list
            function renderMovies(movies) {
                const list = document.getElementById('movieList');
                
                if (movies.length === 0) {
                list.innerHTML = '<div class="no-movies">No movies found. Add some movies to get started!</div>';
                return;
                }
                
                list.innerHTML = movies.map(movie => `
                <div class="movie-card" id="movie-${movie.id}">
                    <div class="movie-header">
                    <h3>${movie.title} (${movie.release_year})</h3>
                    <div class="movie-actions">
                        <button class="btn btn-edit" onclick="showEditForm(${movie.id})">
                        <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-delete" onclick="deleteMovie(${movie.id})">
                        <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                    </div>
                    <div class="movie-details">
                    <p><strong>Genre:</strong> ${movie.genre}</p>
                    <p><strong>Director:</strong> ${movie.director}</p>
                    <p><strong>Synopsis:</strong> ${movie.synopsis}</p>
                    </div>
                    ${movie.poster_url ? `
                    <div class="movie-poster">
                    <img src="${movie.poster_url}" alt="${movie.title} Poster" onerror="this.style.display='none'" />
                    </div>` : ''}
                    <div id="edit-form-${movie.id}" class="edit-form"></div>
                </div>
                `).join('');
            }

            //edit form
            function showEditForm(id) {
                const movieCard = document.getElementById(`movie-${id}`);
                const movieTitle = movieCard.querySelector('h3').textContent;
                const [title, year] = movieTitle.split(' (');
                const release_year = year.replace(')', '');
                
                const details = movieCard.querySelectorAll('.movie-details p');
                const genre = details[0].textContent.replace('Genre: ', '');
                const director = details[1].textContent.replace('Director: ', '');
                const synopsis = details[2].textContent.replace('Synopsis: ', '');
                
                const posterElement = movieCard.querySelector('.movie-poster img');
                const poster_url = posterElement ? posterElement.src : '';
                
                const formContainer = document.getElementById(`edit-form-${id}`);
                formContainer.innerHTML = `
                <form onsubmit="updateMovie(event, ${id})" class="movie-form">
                    <h4>Edit Movie</h4>
                    <div class="form-group">
                    <input type="text" id="edit-title-${id}" value="${title.trim()}" required />
                    </div>
                    <div class="form-group">
                    <input type="text" id="edit-genre-${id}" value="${genre}" required />
                    </div>
                    <div class="form-group">
                    <input type="text" id="edit-director-${id}" value="${director}" required />
                    </div>
                    <div class="form-group">
                    <input type="number" id="edit-release_year-${id}" value="${release_year}" required />
                    </div>
                    <div class="form-group">
                    <textarea id="edit-synopsis-${id}" required>${synopsis}</textarea>
                    </div>
                    <div class="form-group">
                    <input type="url" id="edit-poster_url-${id}" value="${poster_url}" placeholder="Poster URL" />
                    </div>
                    <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-secondary" onclick="hideEditForm(${id})">Cancel</button>
                    </div>
                </form>
                `;
                formContainer.style.display = 'block';
            }

            // Hide edit form
            function hideEditForm(id) {
                const formContainer = document.getElementById(`edit-form-${id}`);
                formContainer.style.display = 'none';
            }

            // Update movie
            async function updateMovie(e, id) {
                e.preventDefault();
                
                const title = document.getElementById(`edit-title-${id}`).value;
                const genre = document.getElementById(`edit-genre-${id}`).value;
                const director = document.getElementById(`edit-director-${id}`).value;
                const release_year = document.getElementById(`edit-release_year-${id}`).value;
                const synopsis = document.getElementById(`edit-synopsis-${id}`).value;
                const poster_url = document.getElementById(`edit-poster_url-${id}`).value;

                try {
                const res = await fetch(`${API_URL}?id=${id}`, {
                    method: 'PUT',
                    headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + API_KEY
                    },
                    body: JSON.stringify({ id, title, genre, director, release_year, synopsis, poster_url })
                });

                if (!res.ok) {
                    throw new Error('Failed to update movie');
                }

                fetchMovies();
                } catch (error) {
                alert(error.message);
                }
            }

            // Add movie
            document.getElementById('addMovieForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                
                const title = document.getElementById('title').value;
                const genre = document.getElementById('genre').value;
                const director = document.getElementById('director').value;
                const release_year = document.getElementById('release_year').value;
                const synopsis = document.getElementById('synopsis').value;
                const poster_url = document.getElementById('poster_url').value;

                try {
                const res = await fetch(API_URL, {
                    method: 'POST',
                    headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + API_KEY
                    },
                    body: JSON.stringify({ title, genre, director, release_year, synopsis, poster_url })
                });

                if (!res.ok) {
                    throw new Error('Failed to add movie');
                }

                fetchMovies();
                document.getElementById('addMovieForm').reset();
                } catch (error) {
                alert(error.message);
                }
            });

            // Delete movie
            async function deleteMovie(id) {
                if (!confirm('Are you sure you want to delete this movie?')) return;
                
                try {
                const res = await fetch(`${API_URL}?id=${id}`, {
                    method: 'DELETE',
                    headers: {
                    'Authorization': 'Bearer ' + API_KEY
                    }
                });

                if (!res.ok) {
                    throw new Error('Failed to delete movie');
                }

                fetchMovies();
                } catch (error) {
                alert(error.message);
                }
            }

            // Search movies
            document.getElementById('movieSearchForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                const query = document.getElementById('searchInput').value.trim();
                const results = document.getElementById('searchResults');
                
                if (!query) {
                results.innerHTML = '<div class="error">Please enter a search term</div>';
                return;
                }
                
                showLoading(results);
                
                try {
                const res = await fetch(`${API_URL}?search=${encodeURIComponent(query)}`, {
                    headers: { 'Authorization': 'Bearer ' + API_KEY }
                });
                
                if (!res.ok) {
                    throw new Error('Search failed');
                }
                
                const data = await res.json();
                
                if (data.length === 0) {
                    results.innerHTML = '<div class="no-results">No movies found matching your search.</div>';
                    return;
                }
                
                results.innerHTML = data.map(movie => `
                    <div class="movie-card">
                    <div class="movie-header">
                        <h3>${movie.title} (${movie.release_year})</h3>
                    </div>
                    <div class="movie-details">
                        <p><strong>Genre:</strong> ${movie.genre}</p>
                        <p><strong>Director:</strong> ${movie.director}</p>
                        <p><strong>Synopsis:</strong> ${movie.synopsis}</p>
                    </div>
                    ${movie.poster_url ? `
                    <div class="movie-poster">
                        <img src="${movie.poster_url}" alt="${movie.title} Poster" onerror="this.style.display='none'" />
                    </div>` : ''}
                    </div>
                `).join('');
                } catch (error) {
                showError(results, error.message);
                }
            });
            document.addEventListener('DOMContentLoaded', () => {
                fetchMovies();
            });
        </script>
    </body>
   
</html>