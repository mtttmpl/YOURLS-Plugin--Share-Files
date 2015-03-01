YOURLS Plugin: Upload and Shorten
=================================

Plugin for [YOURLS](http://yourls.org) `1.7+`.

Description
-----------
Upload files to your server and create short-URLs to them in one step. Now you can share your files using shortlinks as well as URL´s. 

Installation
------------
1. Navigate to the folder `./user/plugins/` inside your YOURLS-install directory

2. *Either* clone this repo using `git` *or*  
  create a new folder named ´Upload-and-Shorten´ and  
  download all files from here and drop them into that directory. 

3. * open `./user/config.php` in your YOURLS-directory with any text editor
   * add two definitions at the end of that file:  
   `define( 'SHARE_URL', 'http://my.domain.tld/directory/' );`  
   `define( 'SHARE_DIR', '/full/path/to/httpd/directory/' );`  
   (both must point to the (existing) directory where your files should be uploaded and accessed from the web)
   * If necessary create a folder matching the name you defined in the above setting 
   * Depending on your webserver´s setup you may have to 'chmod +rw /full/path/to/httpd/directory' 

4. Go to the Plugins administration page (*eg* `http://sho.rt/admin/plugins.php`) and activate the plugin.

5. Have fun!

6. Consider helping with translations.

License
-------
Free for personal use only. 
If you want to make money with it you have to contact me first.

Localization (l10n)
--------------------
This plugin supports **localization** (translations in your language). 
**For this to work you need at least YOURLS v1.7 from March 1, 2015**. Earlier versions will basically work fine, except they will not translate into other languages because of a minor bug in the YOURLS-code. Just upgrade to the latest YOURLS version. 

The plugin talks English per default, translation files for German are included in the folder `i18n/`. Remember to define your locale in `user/config.php` like this:
> define( 'YOURLS_LANG', 'de_DE' ); 

Looking for translators
-----------------------
If you're able and willing to provide translations, please [read this](http://blog.yourls.org/2013/02/workshop-how-to-create-your-own-translation-file-for-yourls/) and contact me for further instructions. Any help will be greatly appreciated by your fellow countrymen!

Donations
---------
The more you use it the more you'll like it. And if you do, remember someone spends his time for improving it. If you want say thanks for that, just [buy him a coffee](https://fredls.net/donate). This will certainly motivate him to push further enhancements. 
Just for You!  ![](https://fredls.net/lTh0sqmQ) and him :)
