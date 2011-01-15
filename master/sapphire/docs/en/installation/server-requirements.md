# Server Requirements

We try to be as wide as possible in our server requirements.  However, there are some limitations.
Please check our [suggested-web-hosts](http://doc.silverstripe.org/old/suggested-web-hosts) (and add your host if the installation was successful).

If you're running Windows, we recommend using the [Windows Platform Installer](windows-pi) or
[WAMP](windows-wamp).

If you're using Mac OS X, we recommend the [MAMP package](http://www.mamp.info/en/mamp.html), see
[Installing on Mac OSX](mac-osx) for instructions on installation. If you are using any other packages or
setups, or you are on linux, please pay careful attention to the requirements below.

## PHP

PHP5 is required. The following are further details and recommendations for how to properly set up PHP for use with
SilverStripe.

*  **PHP 5.2.0+ recommended**, PHP as low as 5.1.0 have been known to work, but for best results 5.2.0+ is recommended.
(see http://www.gophp5.org)
*  Required modules. Most of the time these modules can just be enabled in the php.ini, however support for these need
to be compiled into PHP, which may not be the case if you are not using [WAMP](http://www.wampserver.com/en/) or
[MAMP](http://www.mamp.info/en/mamp.html).
    * dom
    * gd2
    * hash
    * iconv
    * mbstring
    * mysql or pgsql, if you use one of these databases
    * session
    * simplexml
    * tokenizer
    * xml
*  Set the maximum memory to at least 48 mb.  SilverStripe can be resource hungry for some intensive operations. Note:
You can sometimes increase the maximum memory by adding a "ini_set("memory_limit","48M");" line to sapphire/main.php
*  Using a PHP accelerator or opcode cache (e.g. [xcache](http://trac.lighttpd.net/xcache/)) is **strongly**
recommended to for performance reasons - SilverStripe creates large cache files as PHP code that are quite slow to open
without an opcode cache.
*  Gentoo Linux distribution USE-Flags ([related forum
topic](http://silverstripe.org/installing-silverstripe/show/281700)): `berkdb bzip2 cgi cli crypt ctype curl exif
flatfile ftp gd gdbm hash iconv imap ipv6 mysql mysqli ncurses nls pcre readline reflection session simplexml snmp spell
spl ssl threads tokenizer truetype unicode xml xmlreader xmlwriter xpm zip zlib`
*  PHP safe mode off.
* If you have issues with sessions and cookies ensure magic_quotes_gpc is disabled in your php.ini.

If you need information on your PHP configuration, create a php file with the following content:

	:::php
	<?php
	phpinfo();


If you visit this file in your web browser, it will give a full list of the configuration of PHP.

## Database

*  **MySQL 5.0+** (out of the box)
*  **PostgreSQL 8.3+** ([download](http://www.silverstripe.org/postgresql-module/))
*  **SQLite 3+** ([download](http://silverstripe.org/sqlite-database/))
*  **SQL Server 2008** ([download](http://www.silverstripe.org/microsoft-sql-server-database))

Note: MySQL 4.1 has been known to work, but is no longer supported.

## HTTP Server

SilverStripe will work on the following web servers:

*  Apache with mod_rewrite and "AllowOverride All" set
*  IIS 7.x with [URL Rewrite Module](http://www.iis.net/expand/URLRewrite) (installer supported in SilverStripe 2.4+)

SilverStripe *may* work on the following, but it has not been tested by the core team:

*  IIS 5 and 6
*  lighttpd

Some detailed notes on web server setup:

*  Apache 1.3+/2.0+/2.2+ with [mod_rewrite](http://www.workingwith.me.uk/articles/scripting/mod_rewrite), and the
ability to set rewriting rules in .htaccess files via "Allow Override".

* The following is an example of the Directory directive from the Apache configuration file:

	    <Directory "/location/of/silverstripe">
	        Options FollowSymLinks SymLinksifOwnerMatch
	        AllowOverride All
	        Order allow,deny
	        Allow from all
	    </Directory>


* The Apache server needs to be restarted for the configuration changes to take effect.
* We suggest using mod_deflate/gzip to compress HTML/CSS/JS etc and thus improve page loading times *this applies to
any website constructed , not just to SilverStripe*
*  lighttpd 1.4+ with access to the server configuration. (See [installation-on-lighttpd](/topics/installation/installation-on-lighttpd)
*  Silverstripe makes uses the geoiplookup to get the visitors' location (with an ip2country function).  In order for
this to work, you must have the geoiplookup application installed on your server. Silverstripe will run fine without it.

## Operating System

SilverStripe can run on OS X, Windows, Linux and BSD.

*  Windows XP SP3+ (Windows 7 or Server 2008 is recommended)
*  Mac OS X 10.4+.  Earlier versions may work but we haven't tested them and can't provide support.
*  Linux/BSD.  The kernel doesn't really matter as long as you're running the correct HTTP server.

* File permissions: To install and run SilverStripe, You need to be able to set some files (e.g. the 'assets'
folder) to be writable by the user the webserver is running as (e.g. wwwrun). Having shell access or a control panel (or
an FTP server that allows file permission changing) is helpful.

## Browser

Make sure that Cookies have been enabled for the web site for the installation and management of the SilverStripe CMS.
