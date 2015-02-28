YOURLS Plugin- Share-Files
==========================

Upload and share files with YOURLS. Now you can share your files using shortlinks as well as URL's. 

Setup and Installation
----------------------------
* Download the plugin
* Upload the share-files folder to the /user/plugins/ directory in your YOURLS-directory
* open user/config.php in your YOURLS-directory with any text editor
* add two definitions at the end of the file:
* define( 'SHARE_URL', 'http://my.domain.tld/directory/' );
* define( 'SHARE_DIR', '/full/path/to/httpd/directory/' );
* both must point to the (existing) directory where yor files should be uploaded and accessed from the web
* The trailing slashes (/) are important!
* If necessary create a folder matching the folder name above and run 'chmod r+w folder'
* go to the plugins page in your YOURLS admin interface and activate the plugin
