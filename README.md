Datashake API Integration
I have created two PHP scripts to demonstrate how to work with the Datashake API for extracting reviews from various sources.

1. static_review.php (Demo Script)
This script serves as a demo. It includes a placeholder for a company name, which showcases how to retrieve static reviews for a predefined business. This file can be used to see a basic example of how the API works with a static company name.

2. datashake.php (Main Script)
This script contains three input fields that allow dynamic interaction with the Datashake API. Here’s how each section works:

a. Google Location Search:
This feature lets users search for a location by name. Once the search is submitted, the API creates a job ID, which signifies that the reviews crawl has begun. Users can use this job ID to track the progress of the crawl.

b. Get Reviews by URL:
In this section, users can input a URL (e.g., a Google Maps URL) to fetch reviews for a specific business or location. Similar to the first feature, a job ID will be generated when the process starts, and users can track the status of this job to retrieve the final review results.

c. Retrieve Reviews by Job ID:
Once a job ID has been created (from either the Google search or the URL input), users can input that job ID into this third field. If the crawl status is marked as "complete," the script will display the final reviews that have been retrieved from the source.


This script setup provides a flexible way to interact with the Datashake API, allowing users to search for reviews by location, URL, or existing job ID.