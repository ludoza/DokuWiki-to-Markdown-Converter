# Manual installation on Windows using IIS

Install SilverStripe manually on Windows using IIS as the web server. If you are not confident in installing web server software manually on Windows,
it is recommended you using the [Web Platform Installer](windows-pi) method instead, which will do this for you.

## IIS 7.x

IIS 7.x ships with Windows Vista, Windows 7, Windows Server 2008 and Windows Server 2008 R2.

A [full guide is available](windows-manual-iis-7.md) to assist installing SilverStripe on these versions of Windows.

## IIS 6

IIS 6 ships with Windows Server 2003.

This [guide](windows-manual-iis-6) will assist installing SilverStripe on this older version of Windows.

If it all possible, it is recommend you upgrade to at least Windows Server 2008 or Windows Vista so you can take advantage of IIS 7.x, which has features
such as native URL rewriting support.

## Installing on IIS 5.1 and 6.0

These are additional notes about installing SilverStripe on IIS 5.1 and IIS 6.

If using Windows Server 2003 with IIS 6, there is a [guide available](installation-manual-iis-6) which will assist you installing SilverStripe.

  * Microsoft has no URL rewriting module for anything less than IIS 7.x. This will mean your URLs are like yoursite.com/index.php/about-us rather than yoursite.com/about-us. However, if you do want friendly URLs you must you must buy or use other URL rewriting software: 
    * [IIRF](http://iirf.codeplex.com/)
    * [ISAPI_Rewrite](http://www.helicontech.com/download-isapi_rewrite3.htm) (The freeware, lite version should be fine for simple installations)
    * If you have 64-bit windows, you can try [this](http://www.micronovae.com/ModRewrite/ModRewrite.html)
  * Instructions for installing PHP IIS 5.1 and 6 may be different that for 7. See [http://learn.iis.net/page.aspx/248/configuring-fastcgi-extension-for-iis60/](http://learn.iis.net/page.aspx/248/configuring-fastcgi-extension-for-iis60/)
  * On XP, you need to disable 'Check that file exists' (See [installation-on-windows-pi](installation-on-windows-pi))

Matthew Poole has expanded on these instructions [with a tutorial](http://cubiksoundz.blogspot.com/2008/12/tech-note-installing-silverstripe-cms.html).
