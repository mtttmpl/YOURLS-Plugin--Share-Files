<?php
/*
Plugin Name: Upload & Shorten
Plugin URI: https://github.com/fredl99/YOURLS-Upload-and-Shorten
Description: Upload a file and create a short-YOURL for it in one step.
Based on: "Share Files" by Matt Temple
Forked from: http://www.mattytemple.com/projects/yourls-share-files
Version: 1.2
Author: Fredl
Author URI: https://github.com/fredl99
*/

// No direct call
if( !defined( 'YOURLS_ABSPATH' ) ) die();

// Register our plugin admin page
yourls_add_action( 'plugins_loaded', 'my_upload_and_shorten_add_page' );

function my_upload_and_shorten_add_page() {
	// load custom text domain
	yourls_load_custom_textdomain( 'upload-and-shorten', dirname(__FILE__) . '/i18n/' );
	// create entry in the admin's plugin menu
	yourls_register_plugin_page( 'upload-and-shorten', 'Upload & Shorten', 'my_upload_and_shorten_do_page' );
	// parameters: page slug, page title, and function that will display the page itself
}

// Display admin page
function my_upload_and_shorten_do_page() {
	// Check if a form was submitted
	if(isset($_POST['submit'])) $my_save_files_message = my_upload_and_shorten_save_files();

	// prepare i18n messages
	$my_upload_and_shorten_domain = 'upload-and-shorten';
	$my_i18n = array();
	$my_i18n[1] = yourls_esc_html__('Upload & Shorten', $my_upload_and_shorten_domain );
	$my_i18n[2] = yourls_esc_html__('Here you can upload a file to your webserver and create a short-URL for it.', $my_upload_and_shorten_domain );
	$my_i18n[3] = yourls_esc_html__('Select file to upload: ', $my_upload_and_shorten_domain );
	$my_i18n[4] = yourls_esc_html__('Optional custom keyword: ', $my_upload_and_shorten_domain );
	$my_i18n[5] = yourls_esc_html__('Randomize filename ', $my_upload_and_shorten_domain );
	$my_i18n[6] = yourls_esc_attr__('  Go!  ', $my_upload_and_shorten_domain );

	// input form
	echo '
	<h2> '.$my_i18n[1].' </h2>
	<p> '.$my_i18n[2].' </p>
	<form method="post" enctype="multipart/form-data"> <fieldset>
	<p><label for="file_upload">' .$my_i18n[3]. '</label> <input type="file" id="file_upload" name="file_upload" /></p>
	<p><label for="custom_keyword">' .$my_i18n[4]. '</label> <input type="text" id="custom_keyword" name="custom_keyword" /></p>
	<p><input type="checkbox" id="randomize_filename" name="randomize_filename" checked="checked" /><label for="randomize_filename">' .$my_i18n[5]. '</label> <small>(mypicture.jpg -> http://domain.tld/9a3e97434689.jpg)</small></p>
	<p><input type="submit" name="submit" value="' .$my_i18n[6]. '" /></p>
	</fieldset></form>
	<strong>' .$my_save_files_message. '</strong>
	<div id="footer">This plugin is based on the plugin <a href="http://www.mattytemple.com/projects/yourls-share-files" target="_blank">"Share Files" by Matt Temple </a> with a few enhancements. The more you use it the more you\'ll like it! </div>';
	}

// Update option in database
function my_upload_and_shorten_save_files() {
	// once again for translations:
	$my_upload_and_shorten_domain = 'upload-and-shorten';

	// did the user select any file?
	if ($_FILES['file_upload']['error'] == UPLOAD_ERR_NO_FILE) {
		return yourls_esc_html__('You need to select a file to upload.', $my_upload_and_shorten_domain);
	}
	
	// yes!
	$my_url = SHARE_URL;	// has to be defined in user/config.php like this: 
					// define( 'SHARE_URL', 'http://my.domain.tld/directory/' );

	$my_uploaddir = SHARE_DIR;	// has to be defined in user/config.php like this: 
					// define( 'SHARE_DIR', '/full/path/to/httpd/directory/' );	

	$my_extension = pathinfo($_FILES['file_upload']['name'], PATHINFO_EXTENSION);
	$my_filename = pathinfo($_FILES['file_upload']['name'], PATHINFO_FILENAME);

	if(isset($_POST['randomize_filename'])) {
		// make up a random name for the uploaded file
		// see http://www.mattytemple.com/projects/yourls-share-files/?replytocom=26686#respond
		$my_safe_filename = substr(md5($my_filename.strtotime("now")), 0, 12);
		// end randomize filename
	}
	else {
		// original code:
		$my_filename_trim = trim($my_filename);
		$my_RemoveChars  = array( "([\40])" , "([^a-zA-Z0-9-])", "(-{2,})" ); 
		$my_ReplaceWith = array("-", "", "-"); 
		$my_safe_filename = preg_replace($my_RemoveChars, $my_ReplaceWith, $my_filename_trim); 
		// end original code
	}

	// avoid duplicate filenames
	$my_count = 2;
	$my_path = $my_uploaddir.$my_safe_filename.'.'.$my_extension;
	$my_final_file_name = $my_safe_filename.'.'.$my_extension;
	while(file_exists($my_path)) {
		$my_path = $my_uploaddir.$my_safe_filename.'-'.$my_count.'.'.$my_extension;
		$my_final_file_name = $my_safe_filename.'-'.$my_count.'.'.$my_extension;
		$my_count++;	
	}
	
	// move the file from /tmp/ to destination and initiate link creation
	if(move_uploaded_file($_FILES['file_upload']['tmp_name'], $my_path)) {
		$my_custom_keyword = NULL;
		if(isset($_POST['custom_keyword']) && $_POST['custom_keyword'] != '') {
			$my_custom_keyword = $_POST['custom_keyword'];
		}
		
		$my_short_url = yourls_add_new_link($my_url.$my_final_file_name, $my_custom_keyword, $my_final_file_name);
		
		return yourls_esc_html__('Upload finished. This is your short-URL: ', $my_upload_and_shorten_domain) . '<a href="' . $my_short_url['shorturl'] . '" target="_blank">' . $my_short_url['shorturl'] . '</a></strong>';
	}
	else {
		return yourls_esc_html__('Upload failed! Something went wrong, sorry! :(', $my_upload_and_shorten_domain);
	}
}
