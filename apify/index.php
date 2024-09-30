<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Maps Reviews Scraper</title>
    <!-- Bootstrap CSS -->
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
			<label class="mb-2">Business Details</label>
            <input id="autocomplete" placeholder="Enter a business name" type="text" class="form-control mb-4">
            <input id="placeUrl" type="hidden" class="form-control" placeholder="Enter Google Maps Place URL">
            <button class="btn btn-primary mt-3" onclick="fetchReviews()">Fetch Reviews</button>
            
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
	let currentReviews = [];  // Array to store reviews
	let currentPage = 1;  // Initial page
	const reviewsPerPage = 15;  // Reviews to show per page

	function initialize() {
        var autocomplete = new google.maps.places.Autocomplete(
            document.getElementById('autocomplete'),
            { types: ['establishment'] }
        );

        autocomplete.addListener('place_changed', function() {
            var place = autocomplete.getPlace();

            if (!place.place_id) {
                alert('No details available for the selected place!');
                return;
            }

            var service = new google.maps.places.PlacesService(document.createElement('div'));
            service.getDetails({ placeId: place.place_id }, function(placeDetails, status) {
                if (status === google.maps.places.PlacesServiceStatus.OK) {
					console.log(placeDetails.url);
					const place_url = placeDetails.url;
					document.getElementById('placeUrl').value = place_url;
                } else {
                    alert('Could not fetch reviews: ' + status);
                }
            });
        });
    }
	
    async function fetchReviews() {
    const placeUrl = document.getElementById('placeUrl').value;
	console.log('placeUrl: ' + placeUrl);
    const apiToken = 'apify_api_voUYJt2KY84Kd8yqKIzd8qER8B7NJX2hVJPn';  // Replace with your valid Apify API Token
    const apiUrl = 'https://api.apify.com/v2/acts/compass~google-maps-reviews-scraper/run-sync-get-dataset-items?token=apify_api_a9Hsa1nMYYLyU0AnrtkLIV0QuJVxxZ1lWv41';  // Use backticks for template literals
 
    const payload = {
        startUrls: [
            { url: placeUrl }
        ],
        maxReviews: 50,
        reviewsSort: "newest",
        language: "en",
        personalData: true
    };
	
	// Show loader before starting the fetch
    document.getElementById('loader').style.display = 'block';

    try {
        const response = await fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        if (!response.ok) {
            throw new Error('Failed to fetch reviews');
        }

        const reviews = await response.json();
		currentReviews = reviews; // Store fetched reviews
		displayReviews(reviews, 1);  // Display the first page
		setupPagination(reviews.length);  // Setup pagination buttons
    } catch (error) {
        console.error('Error fetching reviews:', error.message);
    }
	finally {
		// Hide loader once the request is complete
		document.getElementById('loader').style.display = 'none';
	}
}

	function displayReviews(reviews, page) {
        const reviewContainer = document.getElementById('review-container');
        reviewContainer.innerHTML = ''; // Clear previous reviews

		// Pagination logic: Show 10 reviews per page
		const start = (page - 1) * reviewsPerPage;
		const end = page * reviewsPerPage;
		const paginatedReviews = reviews.slice(start, end);

        if (!paginatedReviews || paginatedReviews.length === 0) {
            reviewContainer.innerHTML = '<p>No reviews available.</p>';
            return;
        }

        paginatedReviews.forEach(review => {
            const reviewCard = `
                <div class="col-md-12 mt-4">
                    <div class="card review-card">
                        <div class="row g-0">
                            <!-- Profile Section -->
							
                            <div class="col-4 review-header d-flex flex-column justify-content-center align-items-center">
								<a href="${review.reviewUrl}">
									<img src="${review.reviewerPhotoUrl || 'photos/default_profile.png'}" alt="Reviewer Profile" class="profile-img">
								</a>
                                <div class="reviewer-info">
                                    <div class="reviewer-name">${review.name}</div>
                                    <div class="reviewer-id">Review ID: ${review.reviewerId}</div>
                                    <div class="review-date">${new Date(review.publishedAtDate).toLocaleDateString()}</div>
                                    <div class="stars">${getStarIcons(review.stars)}</div>
                                    <div class="total_reviews">Total Reviews: ${review.reviewerNumberOfReviews}</div>
                                    <div class="local_guide">Local Guide Flag: ${review.isLocalGuide}</div>
                                    <div class="rating">${review.stars < 3 ? 'Negative' : 'Positive'}</div>
                                </div>
                            </div>
							
                            <!-- Comment Section -->
							
                            <div class="col-5 comment-section">
								<a href="${review.reviewUrl}">
									<div class="comment-header">
										<img src="photos/7123025_logo_google_g_icon.png" alt="Google Logo" class="google-logo">
									</div>
									<p>${(review.text && review.text.trim()) ? review.text : 'No review given'}</p>
									
									<!-- Responsive review images -->
									<div class="review-images">
										${
											review.reviewImageUrls && review.reviewImageUrls.length > 0 
											? review.reviewImageUrls.map(imageUrl => `
												<div class="review-image-wrapper">
													<img src="${imageUrl}" alt="Review Image" class="review-img">
												</div>
											  `).join('')
											: '<p>No images available</p>'
										}
									</div>
								</a>
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

function setupPagination(totalReviews) {
    const paginationContainer = document.getElementById('pagination');
    paginationContainer.innerHTML = '';  // Clear previous pagination

    const totalPages = Math.ceil(totalReviews / reviewsPerPage);

    // Helper function to create a page item
    function createPageItem(page, isActive = false) {
        const li = document.createElement('li');
        li.classList.add('page-item');
        if (isActive) {
            li.classList.add('active');
        }
        li.innerHTML = `<a class="page-link" href="javascript:void(0)" onclick="goToPage(${page})">${page}</a>`;
        paginationContainer.appendChild(li);
    }

    // Helper function to create dots (...)
    function createDots() {
        const li = document.createElement('li');
        li.classList.add('page-item', 'disabled');
        li.innerHTML = `<a class="page-link" href="javascript:void(0)">...</a>`;
        paginationContainer.appendChild(li);
    }

    // Show the first three pages
    for (let i = 1; i <= Math.min(3, totalPages); i++) {
        createPageItem(i, i === currentPage);
    }

    // Check if we need dots after the first three pages
    if (currentPage > 4) {
        createDots();  // Add dots if current page is beyond page 4
    }

    // Show pages around the current page (up to 2 pages before and after)
    const startPage = Math.max(4, currentPage - 1);
    const endPage = Math.min(currentPage + 1, totalPages - 3);

    for (let i = startPage; i <= endPage; i++) {
        createPageItem(i, i === currentPage);
    }

    // Check if we need dots before the last three pages
    if (currentPage < totalPages - 3) {
        createDots();  // Add dots if current page is far from the last three pages
    }

    // Show the last three pages
    for (let i = Math.max(totalPages - 2, 4); i <= totalPages; i++) {
        createPageItem(i, i === currentPage);
    }
}

function goToPage(page) {
    currentPage = page;
    displayReviews(currentReviews, page);  // Display reviews for the selected page
    setupPagination(currentReviews.length);  // Re-render pagination with updated active page
	// Prevent scrolling to the top of the page
    const reviewContainer = document.getElementById('review-container');
    reviewContainer.scrollIntoView({ behavior: 'smooth' }); // Smooth scroll back to the review container
}




    function getStarIcons(rating) {
        const fullStars = Math.floor(rating);
        const halfStar = rating % 1 >= 0.5 ? 1 : 0;
        const emptyStars = 5 - fullStars - halfStar;

        return `
            ${'<i class="fas fa-star"></i>'.repeat(fullStars)}
            ${'<i class="fas fa-star-half-alt"></i>'.repeat(halfStar)}
            ${'<i class="far fa-star"></i>'.repeat(emptyStars)}`;
    }
	
	google.maps.event.addDomListener(window, 'load', initialize);
</script>
</body>

</html>
