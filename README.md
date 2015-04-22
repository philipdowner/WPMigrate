WordPress Migration
=====================

The purpose of this script is to quickly and easily allow the user to update all database references for a WordPress website. Instead of doing a manual, find-and-replace of a MySQL text file, this script connects to your WordPress database and does all of the heavy lifting for you.

**WARNING!** This script modifies the database as configured in your wp-config.php file. You should make a full backup of your database before using this script.

**SECURITY NOTICE** This script should not be left on a live web server as it offers direct access to your database. When you're done with your migration, delete the script. I/we assume no responsibility if you leave the file on your server and the site is compromised.

Script Installation
----------------------

1. Ensure your wp-config.php file has the correct database connection settings.
2. Upload the wp-migrate.php file into the root directory of your WordPress installation.
3. Point your web browser to the script (like http://mywebsitedomain.com/wp-migrate.php)
4. If your wp-config.php file is setup correctly, the script will determine the current "old" website URL.
5. Set the "New URL" field to the absolute URL you'd like the database updated to.

Serialized Database Values
---------------------------

This script does not handle serialized database values. If this is a concern, you should consider the [database migration tool](https://github.com/interconnectit/Search-Replace-DB/) from interconnect.it.

Additional Notes
-----------------------

* Depending on the plugins you use, you may get PHP error notices about files not being included. You can generally ignore these.