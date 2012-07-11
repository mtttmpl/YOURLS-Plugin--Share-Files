<?php
/*
Plugin Name: Share Files
Plugin URI: http://www.mattytemple.com.org/projects/yourls-share-files/
Description: A simple plugin that allows you to easily share files
Version: 1.0
Author: Matt Temple
Author URI: http://www.mattytemple.com/
*/

// Register our plugin admin page
yourls_add_action( 'plugins_loaded', 'matt_share_files_add_page' );
function matt_share_files_add_page() {
	yourls_register_plugin_page( 'share_files', 'Share Files', 'matt_share_files_do_page' );
	// parameters: page slug, page title, and function that will display the page itself
}

// Display admin page
function matt_share_files_do_page() {

	// Check if a form was submitted
	if(isset($_FILES['file_upload']['name'])) {
		matt_share_files_save_files();
	}
	echo '
				<h2>Share Files</h2>
				<p>This plugin allows you to upload and share files online</p>
				<form method="post" enctype="multipart/form-data">
				<p><label for="file_upload">Select file to Upload</label> <input type="file" id="file_upload" name="file_upload" /></p>
				<p><label for="custom_keyword">Custom Keyword</label> <input type="text" id="custom_keyword" name="custom_keyword" /></p>
				<p><input type="submit" value="Upload File" /></p>
				</form>';
}

// Update option in database
function matt_share_files_save_files() {
	$matt_url = 'http://files.matt.mx/';
	$matt_uploaddir = $_SERVER['DOCUMENT_ROOT'].'/files.matt.mx/';
	//
	$matt_extension = pathinfo($_FILES['file_upload']['name'], PATHINFO_EXTENSION);
	$matt_filename = pathinfo($_FILES['file_upload']['name'], PATHINFO_FILENAME);
	$matt_filename_trim = trim($matt_filename);
	$matt_RemoveChars  = array( "([\40])" , "([^a-zA-Z0-9-])", "(-{2,})" ); 
	$matt_ReplaceWith = array("-", "", "-"); 
	$matt_safe_filename = preg_replace($matt_RemoveChars, $matt_ReplaceWith, $matt_filename_trim); 
	$matt_count = 2;
	$matt_path = $matt_uploaddir.$matt_safe_filename.'.'.$matt_extension;
	$matt_final_file_name = $matt_safe_filename.'.'.$matt_extension;
	while(file_exists($matt_path)) {
		$matt_path = $matt_uploaddir.$matt_safe_filename.'-'.$matt_count.'.'.$matt_extension;
		$matt_final_file_name = $matt_safe_filename.'-'.$matt_count.'.'.$matt_extension;
		$matt_count++;	
	}
	/*$test_file = fopen($matt_uploaddir.'test-file.txt', w);
	fwrite($test_file, 'testing file writes');
	fclose($test_file);
	die(); */
	if(copy($_FILES['file_upload']['tmp_name'], $matt_path)) {
		if(isset($_POST['custom_keyword']) && $_POST['custom_keyword'] != '') {
			$matt_custom_keyword = $_POST['custom_keyword'];
			$matt_short_url = yourls_add_new_link($matt_url.$matt_final_file_name, $matt_custom_keyword, $matt_filename);
			echo 'Your file was saved successfully at '.$matt_short_url['shorturl'];
		} else{
			$matt_short_url = yourls_add_new_link($matt_url.$matt_final_file_name, NULL, $matt_filename);
			echo 'Your file was saved successfully at '.$matt_short_url['shorturl'];
		}
	} else {
		echo 'something went wrong when saving your file';
	}
}
