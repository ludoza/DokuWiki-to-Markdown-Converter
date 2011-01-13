# Windows with IIS 7.0+

This page explains installing SilverStripe manually, without the use of (the easy to use) [Web Platform Installer](windows-pi).

## Configure an existing IIS server

First we need to configure the server:

*  We assume that you already have a IIS server running.
*  If you haven't already, [install  PHP using these
instructions](http://learn.iis.net/page.aspx/246/using-fastcgi-to-host-php-applications-on-iis-70/) 
* When you are editing the php.ini, enable the mysql, gd2, and mbstring extensions
*  [Install the Microsoft URL Rewrite Module](http://www.iis.net/expand/URLRewrite)
*  [Install the Fast-CGI admin panel](http://www.iis.net/downloads/default.aspx?tabid=34&g=6&i=1682 ) (not required on
IIS 7.5+)
*  If you're running on MySQL, you will need to [download and install that too](http://mysql.com).

If you are using the SilverStripe development trunk and wish to use MS SQL server, you will need to perform the
following additional server configuration:

*  Install SQL Server client tools
*  Install the [SQL Server Driver for PHP1.1](http://www.microsoft.com/downloads/details.aspx?displaylang=en&FamilyID=ccdf728b-1ea0-48a8-a84a-5052214caad9),
"sqlsrv".

* `<del>`You will have to use the threadsafe version of the library (php_sqlsrv_ts.dll). On some configurations, PHP
will just silently not load a non-threadsafe extension`</del>`.

* IMPORTANT: You should install a non-thread safe version of [PHP](http://windows.php.net/) AND a non-thread safe
version of the [SQL Server Driver for PHP1.1](http://www.microsoft.com/downloads/details.aspx?displaylang=en&FamilyID=ccdf728b-1ea0-48a8-a84a-5052214caad9)
"sqlsrv". VC9 of both is also preferred.

## Install SilverStripe

Then it's time to install the SilverStripe installation package, and modify it to work with IIS:

*  Download SilverStripe from http://www.silverstripe.org/stable-download/ and unpack it into your web-root, most likely `c:\inetpub\wwwroot`.
*  Create a file called `web.config` in the silverstripe folder with the following content.

		:::xml
		<?xml version="1.0" encoding="UTF-8"?>
		<configuration>
		  <system.webServer>
		    <rewrite>
		      <rules>
		        <rule name="SilverStripe Clean URLs" stopProcessing="true">
		          <match url="^(.*)$" />
		          <conditions>
		            <add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />
		          </conditions>
		          <action type="Rewrite" url="sapphire/main.php?url={R:1}" appendQueryString="true" />
		        </rule>
		      </rules>
		    </rewrite>
		  </system.webServer>
		</configuration>


*  Visit the folder that you just extracted in your web browser.  For example, the URL might be `http://localhost/silverstripe-v2.3/`.  You will be shown the install screen.
*  Enter your MySQL username and password in the boxes provided and press "re-check requirements".  You should be able
to install now.
*  After the installation has finished, delete the index.html and index.php files.
*  You are encouraged to manually configure PHP to allow its mail() command to work, so that SilverStripe can send
emails. This is used to email password reset, ecommerce checkouts, and other areas of the system.

Your site should be running at `http://localhost/silverstripe-v2.3/`

## Troubleshooting

On high traffic sites you might find that you start getting "The FastCGI process exited unexpectedly" errors.  This is
related to PHP/IIS on FastCGI rather than anything SQL specific.  There are plenty of resources on the internet if you
Google "PHP The FastCGI process exited unexpectedly".

To fix this, [follow this guide on best practise
setup](http://learn.iis.net/page.aspx/246/using-fastcgi-to-host-php-applications-on-iis-70/#PHP_Recycling_Behavior) for
FastCGI applications on IIS.

**IMPORTANT:** You should ensure you are using the latest version of the sqlsrv driver. sqlsrv 1.1 is the latest, and works
considerably better than 1.0. If possible, use PHP 5.3 as well.

## Performance

A non-thread safe of PHP is recommended for use with IIS. Thread safety checks are not required, and you'll get a
performance boost by not using a thread safe version. If possible, use a version compiled in VC9 instead of VC6. To use
VC9 on Windows, you need to have .NET Framework 3.5 installed. This should already be installed on Windows Server 2008,
Vista and 7.

You should ensure you are using the latest version of the sqlsrv driver. sqlsrv 1.1 is the latest, and works
considerably better than 1.0 in terms of reliability and performance. If possible, use PHP 5.3 as well.

Try increasing the MaxInstances from 4 to a higher value. For quad core CPUs, a higher value such as 32 should prove to
provide a performance enhancement.

You can also [install wincache for IIS](http://learn.iis.net/page.aspx/678/use-the-windows-cache-extension-for-php/), to
improve performance on live sites.

You can also try installing and enabling static and dynamic content compression for IIS on live sites. Do this in IIS
Manager > Compression.

Doing all of the above should provide a significant performance boost to your site.

## Installing on IIS 5.1 and 6.0

*  Follow the instructions above, but note differences:
* You cannot install the URL Rewriter shown above, because Microsoft has no such plugin for those IIS older versions.
This will mean your URLs are like yoursite.com/index.php/about-us rather than yoursite.com/about-us. If you do want
friendly URLs you must you must buy or use other URL rewriting software 

* IIRF: http://iirf.codeplex.com/
* ISAPI_Rewrite: http://www.helicontech.com/download-isapi_rewrite3.htm
* (The freeware, lite version should be fine for simple installations.)
* If you have 64-bit windows, you can try this one: 
* http://www.micronovae.com/ModRewrite/ModRewrite.html
*  Instructions for installing PHP IIS 5.1 and 6 may be different that for 7. See
http://learn.iis.net/page.aspx/248/configuring-fastcgi-extension-for-iis60/
*  On XP, you need to disable 'Check that file exists' (See [installation-on-windows-pi](windows-pi))

Matthew Poole has expanded on these instructions with this tutorial:
http://cubiksoundz.blogspot.com/2008/12/tech-note-installing-silverstripe-cms.html
