<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the place URL from the POST request
    $placeUrl = urlencode($_POST['placeUrl']);
    
    // Construct the API URL to add the profile
    $apiUrl = "https://app.datashake.com/api/v2/profiles/add?query={$placeUrl}";

    // Initialize cURL session for adding the profile
    $ch = curl_init($apiUrl);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
    curl_setopt($ch, CURLOPT_POST, true); // Set method to POST
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'spiderman-token: ffa22b65ac98d02bddedfba880c9fe1d7af290c1', 
        'Content-Type: application/json',
    ]);

    // Execute the cURL request for adding profile
    $response = curl_exec($ch);

    // Check for cURL errors
    if ($response === false) {
        $error = curl_error($ch);
        echo json_encode(['error' => $error]);
        curl_close($ch);
        exit;
    } else {
        // Decode the JSON response
        $data = json_decode($response, true);

        // If success, use the job_id to fetch the reviews
        //if (isset($data['success']) && $data['success'] === true) {
         // $jobId = $data['job_id'];
            $jobId = '755413779';
            
            

            // Construct the API URL to fetch reviews using the job_id
            $reviewUrl = "https://app.datashake.com/api/v2/profiles/reviews?job_id={$jobId}";

            // Initialize cURL session for fetching reviews
            $chReviews = curl_init($reviewUrl);

            // Set cURL options for the GET request
            curl_setopt($chReviews, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($chReviews, CURLOPT_HTTPHEADER, [
                'spiderman-token: ffa22b65ac98d02bddedfba880c9fe1d7af290c1', // Replace with your actual token
                'Content-Type: application/json',
            ]);

            // Execute the cURL request for reviews
            $reviewResponse = curl_exec($chReviews);

            // Check for errors in fetching reviews
            if ($reviewResponse === false) {
                $error = curl_error($chReviews);
                echo json_encode(['error' => $error]);
              } else {
            // Decode the JSON response and return it
            $reviewData = json_decode($reviewResponse, true);

            // Check if the crawl is still pending or completed
            if (isset($reviewData['crawl_status'])) {
                if ($reviewData['crawl_status'] === 'pending') {
                    echo json_encode(['message' => 'Crawling in progress. Please wait for 3-4 hours to update the status. Do not refresh the page.']);
                } elseif ($reviewData['crawl_status'] === 'complete') {
                    echo json_encode(['reviews' => $reviewData['reviews']]);
                } else {
                    echo json_encode(['message' => 'Crawl status unknown or no reviews available.']);
                }
            } else {
                echo json_encode(['message' => 'Failed to get a valid response from the reviews API.']);
            }
        }

            // Close the cURL session for reviews
            curl_close($chReviews);
        // } else {
        //     echo json_encode(['error' => 'Failed to add profile to queue.']);
        // }
    }

    // Close the cURL session for adding profile
    curl_close($ch);
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Maps Reviews Scraper</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
   
    <style>
        body {
            background-color: #f7f7f7;
        }
        .review-card {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .profile-img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
        }
        .review-header {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f7f7f7;
            padding: 20px;
            border-radius: 15px;
        }
        .reviewer-info {
            text-align: center;
            margin-top: 15px;
        }
        .reviewer-name {
            font-size: 1.2em;
            font-weight: bold;
        }
        .review-date {
            font-size: 0.9em;
            color: gray;
        }
        .rating {
            font-size: 1.1em;
            color: #28a745;
        }
        .comment-section {
            padding: 20px;
        }
        .actions {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: flex-end;
            padding: 20px;
        }
        .google-logo {
            width: 30px;
            height: 30px;
            margin-bottom: 10px;
        }
        .stars {
            color: gold; /* Gold color for stars */
        }
        .action-button {
            width: 120px; /* Fixed width */
            height: 40px; /* Fixed height */
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }
        .action-button i {
            margin-right: 8px; /* Space between icon and text */
        }
        a {
            text-decoration: none;
        }
        #autocomplete {
            width: 300px;
            height: 40px;
            padding: 10px;
        }
        /* Main container holding the images */
        .review-images {
            display: flex;
            flex-wrap: wrap; /* Ensure that the images wrap onto the next line if they exceed the container width */
            gap: 10px; /* Space between the images */
            margin-top: 10px;
        }

        /* Wrapper for each image */
        .review-image-wrapper {
            flex: 1 1 calc(33.333% - 10px); /* Ensures 3 images per row, with a 10px gap between them */
            max-width: calc(33.333% - 10px); /* Maximum width for each image container */
        }

        /* Styling for individual images */
        .review-img {
            width: 100%;  /* Ensure the image takes up the full width of its container */
            height: auto; /* Maintain aspect ratio */
            object-fit: cover; /* Ensures images don't stretch */
            border-radius: 5px; /* Optional: Adds rounded corners to the images */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); /* Optional: Adds a subtle shadow for aesthetics */
        }

        /* Mobile responsiveness: Adjust layout for smaller screens */
        @media (max-width: 768px) {
            .review-image-wrapper {
                flex: 1 1 calc(50% - 10px); /* On smaller screens, display 2 images per row */
                max-width: calc(50% - 10px);
            }
        }

        @media (max-width: 480px) {
            .review-image-wrapper {
                flex: 1 1 100%; /* On very small screens, display 1 image per row */
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h1>Google Maps Reviews Scraper</h1>
            <label class="mb-2">Business Details </label>
            <input id="autocomplete" placeholder="Iris Holidays" type="text" class="form-control mb-4">
            <input id="placeUrl" type="hidden" class="form-control" placeholder="Enter Google Maps Place URL">
             <label class="mb-2">Like: Iris Holidays </label><br>
            <button class="btn btn-primary mt-3" id="fetchReviewsButton">Demo Reviews</button>
        </div>
    </div>
    
    <!-- Loader -->
    <div id="loader" class="text-center mt-3" style="display: none;">
        <div class="spinner-border" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <p>Loading reviews, please wait...</p>
    </div>
    
    <div class="row" id="review-container"></div>
    <ul id="pagination" class="pagination"></ul>

    
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB12PjdSz7-EEPMYkKsafAqRoC8Gx6Fkgk&libraries=places"></script>

<script>
    // Function to initialize Google Places Autocomplete
    function initialize() {
        var autocomplete = new google.maps.places.Autocomplete(
            document.getElementById('autocomplete'),
            { types: ['establishment'] }
        );

        autocomplete.addListener('place_changed', function() {
            var place = autocomplete.getPlace();
            const autocompletes = document.getElementById('autocomplete').value;
            document.getElementById('placeUrl').value = autocompletes;

            if (!place.place_id) {
                alert('No details available for the selected place!');
                return;
            }
        });
    }

    document.getElementById('fetchReviewsButton').addEventListener('click', async () => {
        const placeUrl = document.getElementById('placeUrl').value;

        // Show loader
        document.getElementById('loader').style.display = 'block';

        try {
            const response = await fetch('', {  // Send to the same PHP page
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({ placeUrl }) // Send the place URL
            });

            const result = await response.json();
            console.log('Fetched reviews:', result);

            if (result.error) {
                throw new Error(result.error);
            }

            // Check if there are reviews and display them
            if (result.reviews && result.reviews.length > 0) {
                currentReviews = result.reviews;
                displayReviews(currentReviews, currentPage);
                setupPagination(currentReviews.length);
            } else {
                document.getElementById('review-container').innerHTML = '<p>Crawling in progress. Please wait for 3-4 hours to update the status. Do not refresh the page.</p>';
            }

        } catch (error) {
            console.error('Error fetching reviews:', error.message);
        } finally {
            // Hide loader once the request is complete
            document.getElementById('loader').style.display = 'none';
        }
    });

    // Initialize Google Maps Autocomplete
    google.maps.event.addDomListener(window, 'load', initialize);

    // Reviews pagination logic
    const reviewsPerPage = 10;
    let currentPage = 1;
    let currentReviews = [];  // This will hold the reviews fetched from the API

    // Fetch reviews (use actual reviews from your API response)
    const reviewsData = <?php echo json_encode(['reviews' => $reviewData['reviews']]); ?>;
    currentReviews = reviewsData.reviews || [];

    // Display reviews for the first page
    displayReviews(currentReviews, currentPage);
    setupPagination(currentReviews.length);

    // Function to display reviews for the current page
    function displayReviews(reviews, page = 1) {
        const reviewContainer = document.getElementById('review-container');
        reviewContainer.innerHTML = ''; // Clear existing content

        if (reviews.length === 0) {
            reviewContainer.innerHTML = '<p>No reviews available.</p>';
            return;
        }

        // Pagination logic: calculate start and end index for current page
        const start = (page - 1) * reviewsPerPage;
        const end = start + reviewsPerPage;
        const paginatedReviews = reviews.slice(start, end);

        // Display paginated reviews
        paginatedReviews.forEach(review => {
            const reviewCard = `
                <div class="col-md-12 mt-4">
                    <div class="card review-card">
                        <div class="row g-0">
                            <!-- Profile Section -->
                            <div class="col-4 review-header d-flex flex-column justify-content-center align-items-center">
                                <a href="${review.reviewUrl}">
                                    <img src="${review.profile_picture || 'photos/default_profile.png'}" alt="Reviewer Profile" class="profile-img">
                                </a>
                                <div class="reviewer-info">
                                    <div class="reviewer-name">${review.name}</div>
                                    <div class="reviewer-id">Review ID: ${review.unique_id}</div>
                                    <div class="review-date">${new Date(review.date).toLocaleDateString()}</div>
                                    <div class="stars">${getStarIcons(review.rating_value)}</div>
                                    <div class="rating">${review.rating_value < 3 ? 'Negative' : 'Positive'}</div>
                                </div>
                            </div>

                            <!-- Comment Section -->
                            <div class="col-5 comment-section">
                                <p>${(review.review_text && review.review_text.trim()) ? review.review_text : 'No review given'}</p>
                            </div>

                            <!-- Action Buttons -->
                            <div class="col-3 actions">
                                <button class="btn btn-outline-secondary mb-2 action-button">
                                    <i class="fas fa-share"></i> Share
                                </button>
                                <button class="btn btn-outline-secondary mb-2 action-button">
                                    <i class="fas fa-reply"></i> Respond
                                </button>
                                <button class="btn btn-outline-secondary mb-2 action-button">
                                    <i class="fas fa-tags"></i> Add Tags
                                </button>
                            </div>
                        </div>
                    </div>
                </div>`;
            reviewContainer.insertAdjacentHTML('beforeend', reviewCard);
        });
    }

    // Function to setup pagination based on review count
    function setupPagination(reviewCount) {
        const paginationContainer = document.getElementById('pagination');
        paginationContainer.innerHTML = '';

        const totalPages = Math.ceil(reviewCount / reviewsPerPage);

        for (let i = 1; i <= totalPages; i++) {
            const li = document.createElement('li');
            li.classList.add('page-item', i === currentPage ? 'active' : '');
            li.innerHTML = `<a class="page-link" href="javascript:void(0)" onclick="goToPage(${i})">${i}</a>`;
            paginationContainer.appendChild(li);
        }
    }

    // Function to navigate to a specific page
    function goToPage(page) {
        currentPage = page;
        displayReviews(currentReviews, currentPage);
        setupPagination(currentReviews.length);
    }

    // Helper function to display star icons based on rating
    function getStarIcons(rating_value) {
        const fullStars = Math.floor(rating_value);
        const halfStar = rating_value % 1 >= 0.5 ? 1 : 0;
        const emptyStars = 5 - fullStars - halfStar;

        return `
            ${'<i class="fas fa-star"></i>'.repeat(fullStars)}
            ${'<i class="fas fa-star-half-alt"></i>'.repeat(halfStar)}
            ${'<i class="far fa-star"></i>'.repeat(emptyStars)}`;
    }
</script>
</body>
</html>
