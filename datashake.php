<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the place URL and product URL from the POST request
    $placeUrl = urlencode($_POST['placeUrl']);
    $productUrl = urlencode($_POST['productUrl']);
    $Job_id = $_POST['job_id'];
    
    // Construct the API URLs
    $apiUrl = "https://app.datashake.com/api/v2/profiles/add?query={$placeUrl}";
    $apiProductUrl = "https://app.datashake.com/api/v2/profiles/add?url={$productUrl}";
    $job_id_api = "https://app.datashake.com/api/v2/profiles/add?url={$Job_id}";

    // Function to make a cURL request
    function makeCurlRequest($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'spiderman-token: ffa22b65ac98d02bddedfba880c9fe1d7af290c1',
            'Content-Type: application/json',
        ]);

        // Execute cURL request and check for errors
        $response = curl_exec($ch);
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            return ['error' => $error];
        }

        // Close cURL session and return response
        curl_close($ch);
        return json_decode($response, true);
    }

    // Function to fetch reviews based on job_id
    function fetchReviews($jobId) {
        $reviewUrl = "https://app.datashake.com/api/v2/profiles/reviews?job_id={$jobId}";
        $chReviews = curl_init($reviewUrl);
        curl_setopt($chReviews, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($chReviews, CURLOPT_HTTPHEADER, [
            'spiderman-token: ffa22b65ac98d02bddedfba880c9fe1d7af290c1',
            'Content-Type: application/json',
        ]);

        // Execute the cURL request for reviews
        $reviewResponse = curl_exec($chReviews);
        curl_close($chReviews);

        if ($reviewResponse === false) {
            return ['error' => 'Failed to fetch reviews.'];
        }

        return json_decode($reviewResponse, true);
    }

    // Make the cURL requests for both URLs
    $placeResponse = makeCurlRequest($apiUrl);
    $productResponse = makeCurlRequest($apiProductUrl);
    $job_id_Response = makeCurlRequest($job_id_api);

    // Handle place URL response
    if (isset($placeResponse['success']) && $placeResponse['success'] === true) {
        $placeJobId = $placeResponse['job_id'];
        $placeReviews = fetchReviews($placeJobId);

        if (isset($placeReviews['crawl_status'])) {
            if ($placeReviews['crawl_status'] === 'pending') {
                echo json_encode([
                    'message' => "Crawling for place URL is in progress. Please wait for 3-4 hours to update the status.<br><strong>Note:</strong> You can save this job_id to get the review after 3-4 hours.",
                    'job_id' => $placeJobId,
                    'crawl_status' => $placeReviews['crawl_status']
                ]);
            } elseif ($placeReviews['crawl_status'] === 'complete') {
                echo json_encode([
                    'crawl_status' => $placeReviews['crawl_status'],
                    'reviews' => $placeReviews['reviews']
                ]);
            } else {
                echo json_encode(['message' => 'Unknown crawl status for place URL.']);
            }
        }
    } 
	// else {
        // echo json_encode(['error' => 'Failed to add place URL profile.']);
    // }

    // Handle product URL response
    if (isset($productResponse['success']) && $productResponse['success'] === true) {
        $productJobId = $productResponse['job_id'];
        // $productJobId = '756697113';
        $productReviews = fetchReviews($productJobId);

        if (isset($productReviews['crawl_status'])) {
            if ($productReviews['crawl_status'] === 'pending') {
                echo json_encode([
                    'message' => "Crawling for place URL is in progress. Please wait for 3-4 hours to update the status.<br><strong>Note:</strong> You can save this job_id to get the review after 3-4 hours.",
                    'job_id' => $productJobId,
                    'crawl_status' => $productReviews['crawl_status'],
                    'job_id' => $productReviews['job_id']
                ]);
            } elseif ($productReviews['crawl_status'] === 'complete') {
                echo json_encode([
                    'crawl_status' => $productReviews['crawl_status'],
                    'reviews' => $productReviews['reviews'],
					'job_id' => $productReviews['job_id']
                ]);
            } else {
                echo json_encode(['message' => 'Unknown crawl status for product URL.']);
            }
        }
    }
	// else {
        // echo json_encode(['error' => 'Failed to add product URL profile.']);
    // }
	// Handle JOB ID response
    if (isset($_POST['job_id'])) {
        $ResponseJobId = $Job_id;
        // $productJobId = '756697113';
        $productReviews = fetchReviews($ResponseJobId);

        if (isset($productReviews['crawl_status'])) {
            if ($productReviews['crawl_status'] === 'pending') {
                echo json_encode([
                    'message' => "Crawling for place URL is in progress. Please wait for 3-4 hours to update the status.<br><strong>Note:</strong> You can save this job_id to get the review after 3-4 hours.",
                    'job_id' => $productJobId,
                    'crawl_status' => $productReviews['crawl_status'],
                    'job_id' => $productReviews['job_id']
                ]);
            } elseif ($productReviews['crawl_status'] === 'complete') {
                echo json_encode([
                    'crawl_status' => $productReviews['crawl_status'],
                    'reviews' => $productReviews['reviews'],
					'job_id' => $productReviews['job_id']
                ]);
            } else {
                echo json_encode(['message' => 'Unknown crawl status for product URL.']);
            }
        }
    } 
	// else {
        // echo json_encode(['error' => 'Failed to add product URL profile.']);
    // }

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
            //width: 300px;
            //height: 40px;
            padding: 10px;
        }
		#product_url {
            //width: 300px;
            //height: 40px;
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
		p.else_text {
			margin-top: revert;
		}
		.reviewer-id {
			width: 400px;
		}
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h1>Google Maps Reviews Scraper</h1>
            <label class="mb-2"><strong>Business Details</strong></label>
            <input id="autocomplete" placeholder="Enter a business name" type="text" class="form-control mb-4">
            <input id="placeUrl" type="hidden" class="form-control" placeholder="Enter Google Maps Place URL">
            <button class="btn btn-primary mt-3 mb-3" id="fetchReviewsButton">Fetch Google Reviews</button>
			<button class="btn btn-primary mt-3 mb-3" id="fetchReviewsButton2"><?php echo 'Crawl Status ' . $reviewData['crawl_status']; ?></button>
			<button class="btn btn-primary mt-3 mb-3 fetchReviewsButton4" id="fetchReviewsButton5"><?php echo 'JOB ID ' . $placeJobId; ?></button>
			
			
            <input id="product_url" placeholder="Enter a business URL" type="text" class="form-control mb-4 mt-4">
            <input id="product_job_id" type="hidden" class="form-control" placeholder="Enter Product Job ID">
            <button class="btn btn-primary mt-3" id="fetchURLReviewsButton">Fetch URL Reviews</button>
			<button class="btn btn-primary mt-3" id="fetchReviewsButton3"><?php echo 'Crawl Status ' . $reviewData['crawl_status']; ?></button>
			<button class="btn btn-primary mt-3 fetchReviewsButton4" id="fetchReviewsButton4"><?php echo 'JOB ID ' . $productReviews['job_id']; ?></button>
			
			<input id="job_id" placeholder="Enter JOB ID" type="text" class="form-control mb-4 mt-4">
			<button class="btn btn-primary mt-3" id="fetchReviewsJobId">Fetch Reviews JOB_ID</button>


		
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
    <div class="row">
        <div class="col-md-12">
            <nav>
                <ul id="pagination" class="pagination justify-content-center mt-4"></ul>
            </nav>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB12PjdSz7-EEPMYkKsafAqRoC8Gx6Fkgk&libraries=places"></script>
<script>


    function initialize() {
        var autocomplete = new google.maps.places.Autocomplete(
            document.getElementById('autocomplete'),
            { types: ['establishment'] }
        );

        autocomplete.addListener('place_changed', function() {
            var place = autocomplete.getPlace();
			const autocompletes = document.getElementById('autocomplete').value;
			document.getElementById('placeUrl').value = autocompletes;
            //document.getElementById('placeUrl').value = place.url; // Updated to use place.url
            
            if (!place.place_id) {
                alert('No details available for the selected place!');
                return;
            }
        });
    }
	
	<!--------   URL   ------------------------------------->
	document.getElementById('fetchURLReviewsButton').addEventListener('click', async () => {
        const productUrl = document.getElementById('product_url').value;
		console.log('button');

        // Show loader
        document.getElementById('loader').style.display = 'block';

        try {
            const response = await fetch('', {  // Send to the same PHP page
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({ productUrl }) // Send the place URL
            });

            const result = await response.json();
            console.log('Fetched reviews:', result);

            if (result.error) {
                throw new Error(result.error);
            }

             // Static text to add
				let staticText = "Crawl Status: ";
				let staticJob = "Job ID: ";

				// Update the button text with static text and dynamic crawl status
				document.getElementById('fetchReviewsButton3').innerText = staticText + result.crawl_status;
				document.getElementById('fetchReviewsButton4').innerText = staticJob + result.job_id;

            // Check if there are reviews and display them
            if (result.reviews && result.reviews.length > 0) {
                displayReviews(result.reviews);
            } else {
                // Display the job_id if it's provided in the response
                let message = result.message || 'Crawling in progress. Please wait.';
                if (result.job_id) {
                    message += ` (Job ID: ${result.job_id})`;
                }
                document.getElementById('review-container').innerHTML = `<p class="else_text">${message}</p>`;
            }

        } catch (error) {
            console.error('Error fetching reviews:', error.message);
        } finally {
            // Hide loader once the request is complete
            document.getElementById('loader').style.display = 'none';
        }
    });
	<!--------   JOB ID   ------------------------------------->
	document.getElementById('fetchReviewsJobId').addEventListener('click', async () => {
        const job_id = document.getElementById('job_id').value;
		console.log('button');

        // Show loader
        document.getElementById('loader').style.display = 'block';

        try {
            const response = await fetch('', {  // Send to the same PHP page
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({ job_id }) // Send the place URL
            });

            const result = await response.json();
            console.log('Fetched reviews:', result);

            if (result.error) {
                throw new Error(result.error);
            }

             // Static text to add
				let staticText = "Crawl Status: ";
				let staticJob = "Job ID: ";

				// Update the button text with static text and dynamic crawl status
				// document.getElementById('fetchReviewsButton3').innerText = staticText + result.crawl_status;
				// document.getElementById('fetchReviewsButton4').innerText = staticJob + result.job_id;

            // Check if there are reviews and display them
            if (result.reviews && result.reviews.length > 0) {
                displayReviews(result.reviews);
            } else {
                // Display the job_id if it's provided in the response
                let message = result.message || 'Crawling in progress. Please wait.';
                if (result.job_id) {
                    message += ` (Job ID: ${result.job_id})`;
                }
                document.getElementById('review-container').innerHTML = `<p class="else_text">${message}</p>`;
            }

        } catch (error) {
            console.error('Error fetching reviews:', error.message);
        } finally {
            // Hide loader once the request is complete
            document.getElementById('loader').style.display = 'none';
        }
    });
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

             // Static text to add
				let staticText = "Crawl Status: ";
				let staticJob = "Job ID: ";

				// Update the button text with static text and dynamic crawl status
				document.getElementById('fetchReviewsButton2').innerText = staticText + result.crawl_status;
				document.getElementById('fetchReviewsButton5').innerText = staticJob + result.job_id;

            // Check if there are reviews and display them
            if (result.reviews && result.reviews.length > 0) {
                displayReviews(result.reviews);
            } else {
                // Display the job_id if it's provided in the response
                let message = result.message || 'Crawling in progress. Please wait.';
                if (result.job_id) {
                    message += ` (Job ID: ${result.job_id})`;
                }
                document.getElementById('review-container').innerHTML = `<p class="else_text">${message}</p>`;
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
    const reviewsPerPage = 10;
    let currentPage = 1;
    let currentReviews = [];

    // JSON encoded reviews from PHP
    const reviewsData = <?php echo json_encode(['reviews' => $reviewData['reviews']]); ?>;
    currentReviews = reviewsData.reviews;
	console.log( currentReviews );

    // Display reviews for the first page
    displayReviews(currentReviews, currentPage);
    setupPagination(currentReviews.length);

   function displayReviews(reviews) {
    const reviewContainer = document.getElementById('review-container');
    reviewContainer.innerHTML = ''; // Clear any existing content

    if (reviews.length === 0) {
        reviewContainer.innerHTML = '<p>No reviews available.</p>';
        return;
    }

    reviews.forEach(review => {
        const reviewCard = `
            <div class="col-md-12 mt-4">
                <div class="card review-card">
                    <div class="row g-0">
                        <!-- Profile Section -->
                        <div class="col-4 review-header d-flex flex-column justify-content-center align-items-center">
                            
                                <img src="${review.profile_picture || 'photos/default_profile.png'}" alt="Reviewer Profile" class="profile-img">
                            
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
								<!--
                                <div class="review-images">
                                    ${review.reviewImageUrls && review.reviewImageUrls.length > 0
                                    ? review.reviewImageUrls.map(imageUrl => `
                                        <div class="review-image-wrapper">
                                            <img src="${imageUrl}" alt="Review Image" class="review-img">
                                        </div>
                                      `).join('')
                                    : '<p>No images available</p>'}
                                </div>
								-->
                            
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
