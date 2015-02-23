<?php
/*
Plugin Name: Share Files (fork by fredl99)
Plugin URI: http://www.mattytemple.com/projects/yourls-share-files/
Description: A simple plugin that allows you to easily share files
Version: 1.0.1-F
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
	<p><label for="randomize_filename">Randomize Filename</label> <input type="checkbox" id="randomize_filename" name="randomize_filename" checked="checked" /></p>
	<p><input type="submit" value="Upload File" /></p>
	</form>';
}

// Update option in database
function matt_share_files_save_files() {
	$matt_url = SHARE_URL;	// has to be defined in user/config.php like this: 
				// define( 'SHARE_URL', 'http://my.domain.tld/directory/' );

	$matt_uploaddir = SHARE_DIR;	// has to be defined in user/config.php like this: 
					// define( 'SHARE_DIR', '/full/path/to/httpd/directory/' );	

	$matt_extension = pathinfo($_FILES['file_upload']['name'], PATHINFO_EXTENSION);
	$matt_filename = pathinfo($_FILES['file_upload']['name'], PATHINFO_FILENAME);

	if(isset($_POST['randomize_filename'])) {
		// make up a random name for the uploaded file
		// see http://www.mattytemple.com/projects/yourls-share-files/?replytocom=26686#respond
		$matt_safe_filename = substr(md5($matt_filename.strtotime("now")), 0, 12);
		// end randomize filename
	} else {
		// original code:
		$matt_filename_trim = trim($matt_filename);
		$matt_RemoveChars  = array( "([\40])" , "([^a-zA-Z0-9-])", "(-{2,})" ); 
		$matt_ReplaceWith = array("-", "", "-"); 
		$matt_safe_filename = preg_replace($matt_RemoveChars, $matt_ReplaceWith, $matt_filename_trim); 
		// end original code
	}

	// avoid duplicate filenames
	$matt_count = 2;
	$matt_path = $matt_uploaddir.$matt_safe_filename.'.'.$matt_extension;
	$matt_final_file_name = $matt_safe_filename.'.'.$matt_extension;
	while(file_exists($matt_path)) {
		$matt_path = $matt_uploaddir.$matt_safe_filename.'-'.$matt_count.'.'.$matt_extension;
		$matt_final_file_name = $matt_safe_filename.'-'.$matt_count.'.'.$matt_extension;
		$matt_count++;	
	}
	
	// move the file from /tmp/ to destination and initiate link creation
	if(move_uploaded_file($_FILES['file_upload']['tmp_name'], $matt_path)){
		$matt_custom_keyword = NULL;
		if(isset($_POST['custom_keyword']) && $_POST['custom_keyword'] != '') {
			$matt_custom_keyword = $_POST['custom_keyword'];
		}
		$matt_short_url = yourls_add_new_link($matt_url.$matt_final_file_name, $matt_custom_keyword, $matt_final_file_name);
		echo 'Your file was saved successfully at <a href="'.$matt_short_url['shorturl'].'">'.$matt_short_url['shorturl'].'</a>';
	} 
	else {
		echo 'something went wrong when saving your file';
	}
}
