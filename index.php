<!DOCTYPE html>

<html>

<head>

    <title>FamilyFlix - Cari Film</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>

<body style="font-family: roboto;">

    <center>

        <h1>🎬 FAMILYFLIX</h1>

        

        <input 

            type="text" 

            id="searchInput" 

            placeholder="Ketik judul film..."

            style="padding: 10px; width: 300px; font-size: 16px; margin: 10px;"

        >

        <button 

            id="searchBtn" 

            style="padding: 10px 20px; font-size: 16px; background: #ff9800; color: white; border: none; cursor: pointer;"

        >

            Cari Film

        </button>

        <div id="results">

            <h3 id="resultsTitle">Hasil Pencarian</h3>

            <p id="statusText">Masukkan kata kunci untuk mencari film</p>

            

            <div id="moviesList">

                <!-- Links akan muncul di sini -->

            </div>

        </div>

    </center>

    <script>

        // API URL

        const API_URL = 'https://lb-movapis.ct.ws/movie-api.php?s=';

        

        // DOM Elements

        const searchInput = document.getElementById('searchInput');

        const searchBtn = document.getElementById('searchBtn');

        const resultsTitle = document.getElementById('resultsTitle');

        const statusText = document.getElementById('statusText');

        const moviesList = document.getElementById('moviesList');

        

        // Event Listeners

        searchBtn.addEventListener('click', searchMovies);

        searchInput.addEventListener('keypress', function(e) {

            if (e.key === 'Enter') searchMovies();

        });

        

        // Auto-focus search

        searchInput.focus();

        

        // Search function

        async function searchMovies() {

            const query = searchInput.value.trim();

            

            if (!query) {

                alert('Masukkan judul film!');

                searchInput.focus();

                return;

            }

            

            // Show loading

            resultsTitle.textContent = `Mencari: "${query}"`;

            statusText.textContent = 'Sedang mencari film...';

            moviesList.innerHTML = '';

            

            try {

                const response = await fetch(`${API_URL}${encodeURIComponent(query)}`);

                const data = await response.json();

                

                console.log('API Response:', data); // Debug log

                

                if (data.status === 'success' && data.found && data.data && data.data.length > 0) {

                    displayMovieLinks(data.data, query);

                } else {

                    resultsTitle.textContent = `Hasil untuk: "${query}"`;

                    statusText.textContent = 'Film tidak ditemukan';

                    moviesList.innerHTML = '<p><i>Coba kata kunci lain</i></p>';

                }

            } catch (error) {

                console.error('Error:', error);

                resultsTitle.textContent = 'Error';

                statusText.textContent = 'Gagal menghubungi server API';

                moviesList.innerHTML = `<p>Error: ${error.message}</p>`;

            }

        }

        

        // Display movie links

        function displayMovieLinks(movies, query) {

            resultsTitle.textContent = `Hasil untuk: "${query}" (${movies.length} film)`;

            statusText.textContent = `${movies.length} film ditemukan`;

            

            moviesList.innerHTML = '';

            

            // Create table for better display

            const table = document.createElement('table');

            table.style.width = '80%';

            table.style.margin = '20px auto';

            table.style.borderCollapse = 'collapse';

            

            // Add header

            const header = table.insertRow();

            header.innerHTML = `

                <th style="border: 1px solid #ddd; padding: 10px; text-align: left; background: #f2f2f2;">No</th>

                <th style="border: 1px solid #ddd; padding: 10px; text-align: left; background: #f2f2f2;">Judul Film</th>

                <th style="border: 1px solid #ddd; padding: 10px; text-align: left; background: #f2f2f2;">Kualitas</th>

                <th style="border: 1px solid #ddd; padding: 10px; text-align: left; background: #f2f2f2;">Format</th>

                <th style="border: 1px solid #ddd; padding: 10px; text-align: left; background: #f2f2f2;">Download</th>

            `;

            

            // Add movie rows

            movies.forEach((movie, index) => {

                const row = table.insertRow();

                

                // Clean title

                const cleanTitle = cleanMovieTitle(movie.Title);

                

                row.innerHTML = `

                    <td style="border: 1px solid #ddd; padding: 10px;">${index + 1}</td>

                    <td style="border: 1px solid #ddd; padding: 10px;"><strong>${cleanTitle}</strong></td>

                    <td style="border: 1px solid #ddd; padding: 10px;">${movie.quality || 'HD'}</td>

                    <td style="border: 1px solid #ddd; padding: 10px;">${movie.extension || 'MP4'}</td>

                    <td style="border: 1px solid #ddd; padding: 10px;">

                        <a href="${movie.Video}" 

                           target="_blank" 

                           style="color: #ff9800; text-decoration: none; font-weight: bold;">

                            View👁️

                        </a>

                    </td>

                `;

            });

            

            moviesList.appendChild(table);

            

            // Also add simple list format

            const simpleList = document.createElement('div');

            simpleList.style.margin = '20px';

            simpleList.innerHTML = '<h4>Link Download:</h4>';

            

            movies.forEach((movie, index) => {

                const cleanTitle = cleanMovieTitle(movie.Title);

                const link = document.createElement('a');

                link.href = movie.Video;

                link.target = '_blank';

                link.textContent = `${index + 1}. ${cleanTitle}`;

                link.style.display = 'block';

                link.style.margin = '5px 0';

                link.style.padding = '5px';

                link.style.color = '#ff9800';

                link.style.textDecoration = 'none';

                link.style.border = '1px solid #ddd';

                link.style.borderRadius = '3px';

                

                simpleList.appendChild(link);

            });

            

            moviesList.appendChild(simpleList);

        }

        

        // Clean movie title

        function cleanMovieTitle(title) {

            return title

                .replace(/\./g, ' ')

                .replace(/\s+/g, ' ')

                .replace(/(\d{4})/, '($1)')

                .replace(/720p|1080p|2160p|4k|webrip|bluray|x264|aac|yts|mx|\[.*?\]/gi, '')

                .replace(/[\(\)]/g, ' ')

                .replace(/\s+/g, ' ')

                .trim()

                .substring(0, 50);

        }

        

        // Quick search examples

        function addQuickSearchButtons() {

            const quickSearches = [

                {name: 'Avengers', query: 'avengers'},

                {name: 'Spider-Man', query: 'spider'},

                {name: 'Batman', query: 'batman'},

                {name: 'Superman', query: 'superman'},

                {name: 'Disney', query: 'disney'}

            ];

            

            const quickDiv = document.createElement('div');

            quickDiv.style.margin = '20px 0';

            quickDiv.innerHTML = '<p><strong>Cepat:</strong></p>';

            

            quickSearches.forEach(item => {

                const btn = document.createElement('button');

                btn.textContent = item.name;

                btn.style.margin = '5px';

                btn.style.padding = '8px 15px';

                btn.style.background = '#eee';

                btn.style.border = '1px solid #ccc';

                btn.style.cursor = 'pointer';

                btn.onclick = () => {

                    searchInput.value = item.query;

                    searchMovies();

                };

                quickDiv.appendChild(btn);

            });

            

            // Insert after search button

            searchBtn.parentNode.insertBefore(quickDiv, searchBtn.nextSibling);

        }

        

        // Initialize

        document.addEventListener('DOMContentLoaded', function() {

            addQuickSearchButtons();

            

            // Check URL for search parameter

            const urlParams = new URLSearchParams(window.location.search);

            const searchParam = urlParams.get('search');

            if (searchParam) {

                searchInput.value = searchParam;

                setTimeout(() => searchMovies(), 500);

            }

        });

    </script>

</body>

</html>
