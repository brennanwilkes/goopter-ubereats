<?php

/**
 * Declares constants and functions for retrieving web data, downloading images, and uploading to drive
 *
 * PHP version 7.4.3
 *
 * @category Webscraper
 * @package  Webscraper
 * @author   Brennan Wilkes <brennan@codexwilkes.com>
 * @license  CC BY-NC-ND 4.0 https://creativecommons.org/licenses/by-nc-nd/4.0/
 * @link     n/a
 */

//URL of Uber Eats page
const URL = "https://www.ubereats.com/ca/vancouver/food-delivery/hon-sushi/XAAB10yNTL6wz9qbi2gXfA";

//Image output directory
const OUTPUT = "./products/";

//Google Drive folder ID
const DRIVE_FOLDER_ID = "12di4UQeLacp8Av2D1JAAAo9P2aK361xB";

//Conversion chart of unicode characters
const UNICODE_CONVERSIONS = array(
	"\\u0022" => "\"",
	"\\u0026" => "&",
	"\\u003E" => ">",
	"\\u003C" => "<",
	"%5C" => "\\",
);


/**
 * Retrieves all the products from the Hon Sushi Uber Eats page
 * @return array map of products
 */
function getHonSushiProducts()
{
	//Download full HTML
	$html = file_get_contents(URL);

	//Manually replace raw unicode strings with correct characters
	foreach (UNICODE_CONVERSIONS as $unicode => $char) {
		$html = str_replace($unicode, $char, $html);
	}

	//Parse out JSON data from the bottom of page
	preg_match('/.*{"activeOrders":.*/', $html, $rawJSON);

	//Convert to JSON object
	$json = json_decode($rawJSON[0]);

	//Pull out and return the products "array" map of product information
	return $json
		-> {"stores"}
		-> {"5c0001d7-4c8d-4cbe-b0cf-da9b8b68177c"}
		-> {"data"}
		-> {"sectionEntitiesMap"}
		-> {"e8db9ac7-3349-4e00-915c-b7d048eb5080"};
}

/**
 * Downloads all the images from the Hon Sushi Uber Eats page
 * @param $backupToDrive boolean If images should also be backed up to google drive. Deafults to true
 */
function downloadImages($backupToDrive = true)
{

	//Get products
	$products = getHonSushiProducts();

	//Create output directory
	if (!file_exists(OUTPUT)) {
		mkdir(OUTPUT);
	}

	//Iterate over products
	foreach ($products as $id => $product) {
		//Pull out image URL
		$url = $product -> {"imageUrl"};

		//Only continue on valid URLs
		if (!is_null($url) && strlen($url) > 0) {
			//Parse out file extension
			$temp = explode(".", $url);
			$extension = end($temp);


			//Pull out product title
			$filename = $product -> {"title"};

			//Trim whitespace
			$filename = preg_replace('/^\s+/', "", $filename);
			$filename = preg_replace('/\s+$/', "", $filename);

			//Add file extension
			$filename = $filename . "." . $extension;

			//Download to file
			file_put_contents(OUTPUT . $filename, file_get_contents($url));

			//Backup to cloud
			if ($backupToDrive) {
				uploadFileToDrive($filename, $extension);
			}
		}
	}
}

/**
 * Uploads a given file to google drive
 * @param $filename string File to upload
 * @param $extension string Filetype
 */
function uploadFileToDrive($filename, $extension)
{

	//Include Google Drive modules
	global $googleDriveClient, $googleDriveService;

	//Get image data
	$image = file_get_contents(OUTPUT . $filename);

	//Initialize a new file on google drive
	$file = new Google_Service_Drive_DriveFile();
	$file -> setName($filename);
	$file -> setParents(array(DRIVE_FOLDER_ID));

	//Upload
	$googleDriveService -> files -> create(
		$file,
		array(
			'data' => $image,
			'mimeType' => 'image/' . $extension,
			'uploadType' => 'media'
		)
	);
}
