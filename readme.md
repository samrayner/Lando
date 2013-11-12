Lando
=====

**Sorry, Lando is no longer being actively developed. [Read more here.](http://landocms.com/posts/retiring-lando/)**

---


Lando is a new kind of CMS that lets you manage your website in The Cloud. Just save files in your Dropbox and they'll appear on your site!


---


Requirements
------------
1. A server running PHP 5.3+
2. The [cURL PHP library][curl] installed
3. A [Dropbox][db] account


Installation
------------
1. Download the Lando ZIP archive.
2. Extract the contents of the ZIP and upload to your server.
3. Set the permissions of /app (as well as /app/config & /app/cache if they exist) to 777.
4. Set the permissions of /install and everything in it to 777.
5. Visit your website (at the path where you installed Lando).
6. Follow the installation wizard.


To update from a previous version
---------------------------------
1. Backup your /app/config/ folder, and any custom theme folders from /themes/.
2. Also backup any custom parsers from /app/parsers/ and your .htaccess file if necessary.
3. Download the latest version of Lando.
4. Extract the contents of the ZIP and upload to your server, overwriting existing files and folders.
5. Restore any backups from steps 1 and 2.
6. Visit /admin/, check your settings are correct and click the button to recreate content caches.


---


Read the full documentation at <http://landocms.com/docs/>

If you find a bug or have a feature request, please [file an issue][1].

[1]: https://github.com/samrayner/Lando/issues
[curl]: http://uk3.php.net/curl
[db]: http://dropbox.com
