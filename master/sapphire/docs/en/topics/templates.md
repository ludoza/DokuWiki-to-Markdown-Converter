# Templates

## Syntax

TODO Show a good cross section of built-in-page-controls.md

## Example Template

	:::ss
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
	  <% base_tag %>
	  $MetaTags(false)
	  <title>Bob's Chicken Shack | $Title</title>
	</head>

	<body>
	<div id="Container">
	  <div id="Header">
	    <h1>Bob's Chicken Shack</h1>
	  </div>

	  <div id="Navigation">
	    <% if Menu(1) %>
	    <ul>
	      <% control Menu(1) %>	  
	        <li><a href="$Link" title="Go to the $Title page" class="$LinkingMode">$MenuTitle</a></li>
	      <% end_control %>
	    </ul>
	   <% end_if %>
	  </div>

	  <div id="Layout">
	    $Layout
	  </div>

	  <div id="Footer">
	    <p>Copyright $Now.Year - Bob's Chicken Shack.</p>
	  </div>
	</div>
	</body>
	</html>

## Structure and Inheritance

TODO Take abbreviated descriptions from the first tutorial

## Includes

Within SilverStripe templates we have the ability to include other templates from the Includes directory using the SS
'include' tag. For example, the following code would include the `Includes/SideBar.ss` code:

	:::ss
	<% include SideBar %>

The "include" tag can be particularly helpful for nested functionality. In this example, the include only happens if
a variable is true

	:::ss
	<% if CurrentMember %>
		<% include MembersOnlyInclude %>
	<% end_if %>


## Bundling templates into Themes

TODO

## Including CSS and JavaScript files

See [css](css) and [javascript](javascript) topics.

TODO Using `<% require %>` vs Requirements class in Controller::init(), combine_files, different media types,
typography.css

## Base Tag

The `<% base_tag %>` placeholder is replaced with the HTML base element. Relative links within a document (such as `<img
src="someimage.jpg" />`) will become relative to the URI specified in the base tag. This ensures the browser knows where
to locate your site’s images and css files. So it is a must for templates!

It renders in the template as `<base href="http://www.mydomain.com" />`

## Meta Tags

The `$MetaTags` placeholder in a template returns a segment of HTML appropriate for putting into the `<head>` tag. It
will set up title, keywords and description meta-tags, based on the CMS content and is editable in the 'Meta-data' tab
on a per-page basis. If you don’t want to include the title-tag `<title>` (for custom templating), use
`$MetaTags(false)`.

By default `$MetaTags` renders:

	:::ss
	<title>Title of the Page</title>
	<meta name="generator" http-equiv="generator" content="SilverStripe 2.0" >
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" >

TODO Explain SiteTree properties and SiteTree->MetaTags() overloading

## Casting and Escaping

Method and variables names that deal with strings or arrays of strings should have one of the following 5 prefixes:

*  **RAW_** Raw plain text, as a user would like to see it, without any HTML tags
*  **XML_** Text suitable for insertion into an HTML or XML data-set.  This may contain HTML content, for example if the
content came from a WYSIWYG editor.
*  **JS_** Data that can safely be inserted into JavaScript code.
*  **ATT_** Data that can safely be inserted into an XML or HTML attribute.

The same prefixes are used for both strings and arrays of strings.  We did this to keep things simple: passing a string
with the wrong encoding is a far subtler a problem than passing an array instead of a string, and therefore much harder
to debug.


## Designing reuseable templates

Although SilverStripe is ultimately flexible in how you create your templates, there's a couple of best practices. These
will help you to design templates for modules, and make it easier for other site developers to integrate them into their
own base templates.

* Most of your templates should be Layout templates
* Build your templates as a [[theme]] so you can easily re-use and exchange them
* Your layout template should include a standard markup structure (`<div id="Layout">`$Layout`</div>`)
* Layout templates only include content that could be completely replaced by another module (e.g. a forum thread). It
might be infeasible to do this 100%, but remember that every piece of navigation that needs to appear inside `$Layout`
will mean that you have to customise templates when integrating the module.
*  Any CSS applied to layout templates should be flexible width. This means the surrounding root template can set its
width independently.
*  Don't include any navigation elements in your Layout templates, they should be contained in the root template.
*  Break down your templates into groups of includes.  Site integrators would then have the power to override individual
includes, rather than entire templates.


## Related

 * [themes](/topics/themes)
 * [widgets](/topics/widgets)
 * [images](/reference/image)
 * [Tutorial 1: Building a basic site](/tutorials/1-building-a-basic-site)
 * [Tutorial 2: Extending a basic site](/tutorials/2-extending-a-basic-site)