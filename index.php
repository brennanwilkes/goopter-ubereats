<?php

/**
 * Scrapes the Hon Sushi webpage, downloading all product images to a file, and to a google drive folder
 *
 * PHP version 7.4.3
 *
 * @category Webscraper
 * @package  Webscraper
 * @author   Brennan Wilkes <brennan@codexwilkes.com>
 * @license  CC BY-NC-ND 4.0 https://creativecommons.org/licenses/by-nc-nd/4.0/
 * @link     n/a
 */

if (php_sapi_name() != 'cli') {
	throw new Exception('This application must be run on the command line.');
}

//Import google drive client file from the google drive API quickstart guide
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/googleDriveClient.php';

// Get the API client and construct the service object.
$googleDriveClient = getClient();
$googleDriveService = new Google_Service_Drive($googleDriveClient);

//Import webscraper functions
require __DIR__ . '/webscraper.php';

//Run project
downloadImages();

?>
