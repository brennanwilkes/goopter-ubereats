# Hon Sushi Webscraper

**Installation**

To install this application, first install the dependencies by running
```sh
composer install
```

Next obtain a credentials.json file for the Google Drive API from the [quickstart guide](https://developers.google.com/drive/api/v3/quickstart/php).

**Execution**

To run this program, simply type
```sh
php index.php
```

This program will create a folder called `products`, and populate it with all of the images associated with products from the [Hon Sushi Uber Eats page](https://www.ubereats.com/ca/vancouver/food-delivery/hon-sushi/XAAB10yNTL6wz9qbi2gXfA). These images will be named according to their product name, and will additionally be uploaded to [Google Drive](https://drive.google.com/drive/folders/12di4UQeLacp8Av2D1JAAAo9P2aK361xB?usp=sharing).

**Development**

My first challenge with this coding test was the encoded characters in the source HTML. Upon downloading using PHP, or even curl (for test purposes), I found that many characters such as double-quotes, forward slashes, and angle brackets where replaced with their Unicode representation. I approached this believing it was a bug in my code, but eventually determined that the raw data being sent to be from the Uber Eats page contained these Unicode strings, so I instead switched to a solution where I simply replaced them with their correct values using simple regular expression.  

My next challenge was the images themselves. I have written webscrapers in the past, so I jumped in right away parsing the DOM. However this page in particular follows a data lifecycle where the images are loaded in after the first contextual paint using JavaScript. This meant that at initial page load the page only had a handful of img tags to parse out. To solve this I began searching through the page's source code, looking for the JavaScript which loaded the images, and eventually found a large JSON object in a single-line script tag near the bottom of the page. Using a regular expression I parsed out this JSON object and encoded it as an object. From there it was a simple traversal (hard-coded for this code-test) through the object to the relavent data.  

Next my code parses out the product name, and image URL. It downloads the image data from the web, generates a filename based on the product name and file extension, and saves it to disk. Finally, it interfaces with the Google Drive API and uploads it to the cloud.
