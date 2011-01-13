# Contributing

Any open source product is only as good as the community behind it. You can participate by sharing 
code, ideas, or simply helping others. No matter what your skill level is, every contribution counts.

See http://silverstripe.org/contributing-to-silverstripe for a high level overview on how you can help out.

## Sharing your Opinion

*  [silverstripe.org/forums](http://silverstripe.org/forums): Forums on silverstripe.org
*  [silverstripe-dev](http://groups.google.com/group/silverstripe-dev): Core development mailinglist
*  [silverstripe-documentation](http://groups.google.com/group/silverstripe-documentation): Documentation team mailinglist

## Submitting Patches

If you have created a patch to fix a bug in one the modules - thank you so much! :D To submit the patch,
[register](http://open.silverstripe.com/register) and attach the patch to the appropriate ticket. Please include in the
comment the revision number that the patch is applicable for and a brief outline of what you fixed and how. 
Only use the provided link to submit patches, as it prefills information about owner and ticket-type:

[Submit a patch (requires account on open.silverstripe.org)](http://open.silverstripe.com/newticket?field_type=patch&field_owner=ischommer&attachment=1)

The core team is responsible for reviewing the patches and deciding if they will make it into core.  If
there are any problems they will assign the ticket back to you, so make sure you have an email address loaded into Trac
so that it will notify you! The Trac report [Active Patches](http://open.silverstripe.com/report/10) will let you see
where all the patches are at.

You can create a patch file through the svn diff-command on the command-line. 
More info in the [svn redbook](http://svnbook.red-bean.com/en/1.1/ch03s05.html#svn-ch-3-sect-5.3.2). 
Note: Your code-editor might have a GUI for creating patches.

	# in a working copy folder (e.g /myproject)
	svn diff sapphire/ > ~/patch.diff

*  Submit your patch in *diff -u* or *diff -c format*. 

*  **Check your patches against a svn checkout of the [current trunk](http://open.silverstripe.com/browser/modules)**. 
Please not that the latest stable release will often not be sufficient! (of all modules)

*  If your patch is extensive, discuss it first on the [silverstripe
forum]([[http///www.silverstripe.com/silverstripe-forum/) (optimally before doing any serious coding)

*  Adhere to our [coding conventions](coding-conventions)
*  Provide complete [unit test coverage](/topics/testing) - depending on the complexity of your work, this is a required
step.
*  Do not set milestones. If you think your patch should be looked at with priority, mark it as "critical".
*  Describe specifics on how to test the effects of the patch
*  It's better to submit multiple patches with separate bits of functionality than a big patch containing lots of
changes

*  If your patch involves new files, create a compressed archive for them (including any required directory-structures)
*  Create patches relative to the working copy (*sapphire/main.php* instead of */Users/myuser/sapphire/main.php*)
*  Document your code inline through [PHPDoc](http://en.wikipedia.org/wiki/PHPDoc) syntax. See our 
[API documentation](http://api.silverstripe.org/trunk) for good examples.

*  Also check and refer to wiki documentation on [doc.silverstripe.com](http://doc.silverstripe.com) which needs
changes. Check for any references to functionality deprecated or extended through your patch. Please don't alter the
documentation before your patch is integrated.

*  Remember the [shortcomings](http://subversion.tigris.org/project_tasks.html#svn-augmented-diff) of *svn diff*: Please
document moved files and created/deleted directories separately

## Reporting Security Issues

Report security issues to [security@silverstripe.com](mailto:security@silverstripe.com). Please don't file security
issues in our [bugtracker](http://open.silverstripe.org). In the event of a confirmed vulnerability in SilverStripe
core, we will take the following actions:

*  Acknowledge to the reporter that we’ve received the report and that a fix is forthcoming. We’ll give a rough
timeline and ask the reporter to keep the issue confidential until we announce it.

*  Halt all other development as long as is needed to develop a fix, including patches against the current and one
previous major release (if applicable).

*  We will inform you about resolution and [announce](http://groups.google.com/group/silverstripe-announce) a [new
release](http://silverstripe.org/security-releases/) publically.

You can help us determine the problem and speed up responses by providing us with more information on how to reproduce
the issue: SilverStripe version (incl. any installed modules), PHP/webserver version and configuration, anonymized
webserver access logs (if a hack is suspected), any other services and web packages running on the same server.

## Writing Documentation

### Repositories

*  End-user: [userhelp.silverstripe.com](http://userhelp.silverstripe.com): 
*  Developer Guides: [doc.silverstripe.com](http://doc.silverstripe.com)
*  Developer API Docuumentation: [api.silverstripe.com](http://api.silverstripe.com)

### What to write

* **API Docs**: Written alongside source code, this documents the low-level, technical usage of a class, method or property.
  Not suited for longer textual descriptions, due to the limited support of PHPDoc formatting for headlines.
* **Tutorials**: The first contact for new users, guiding them step-by-step through achievable projects, in a book-like style.
  *Example: Building a basic site*
* **Topics**: Provides an overview on how things fit togehter, the "conceptual glue" between APIs and features. 
  This is where most documentation should live, and is the natural "second step" after finishing the tutorials.
  *Example: Templates, Testing, Datamodel*
* **Howto**: Recipes that solve a specific task or problem, rather than describing a feature.
  *Example: Export DataObjects as CSV, Customizing TinyMCE in the CMS*
* **Reference**: Complements API docs in providing deeper introduction into a specific API. Most documentation
  should fit elsewhere. *Example: ModelAdmin*
* **Misc**: "Meta" documentation like coding conventions that doesn't directly relate to a feature or API. 

See [What to write (jacobian.org)](http://jacobian.org/writing/great-documentation/what-to-write/) for an excellent
introduction to the different types of documentation.

### Structure

* Don't duplicate: Search for existing places to put your documentation. Do you really require a new page, or just a new paragraph
of text somewhere?
* Use PHPDoc in source code: Leave lowlevel technical documentation to code comments within PHP, in [PHPDoc](http://en.wikipedia.org/wiki/PHPDoc) format. 
* Use Markdown in Developer Guides: We have a slightly customized version of Markdown called [SSMarkdown][ss-markdown]
* API and Developer Guides complement each other: Both forms of documenting sourcecode (API and Developer Guides) are valueable ressources.
* Provide context: Give API documentation the "bigger picture" by referring to Developer Guides inside your PHPDoc.
* Make your documentation findable: Documentation lives by interlinking content, so please make sure your contribution doesn't become an
inaccessible island. Your page should at least be linked on the index page in the same folder. It can also appear
as "related content" on other resource (e.g. [topics/search](/topics/search) might link to [howto/search-dataobjects](/howto/search-dataobjects)).
* Avoid FAQs: FAQs are not a replacement of a coherent, well explained documentation. If you've done a good job
documenting, there shouldn't be any "frequently asked questions" left ;)
* Commit early and often: You don't need to completely finish documentation, as long as you mark areas needing refinement.
* Every file should have exactly one `<h1>` headline, roughly matching the filename. It should be short enough to be
used in table of content lists.

### Writing Style

* Write in second plural form: Use "we" instead of "I". It gives the text an instructive and collaborative style.
* Its okay to address the reader: For example "First you'll install a webserver" is good style.
* Write in an active and direct voice
* Mark up correctly: Use preformatted text, emphasis and bold to make technical writing more "scannable".

### Highlighted blocks ###

There are several built-in block styles for highlighting a paragraph of text. 
Please use these graphical elements sparingly.

<div class="hint" markdown='1'>
"Tip box": Adds, deepens or accents information in the main text.
Can be used for background knowledge, or "see also" links.
</div>

Code:

	<div class="hint" markdown='1'>
	...
	</div>

<div class="notice" markdown='1'>
"Notification box": Technical notifications relating to the main text.
For example, notifying users about a deprecated feature.
</div>

Code:

	<div class="notice" markdown='1'>
	...
	</div>

<div class="warning" markdown='1'>
"Warning box": Highlight a severe bug or technical issue requiring
a users attention. For example, a code block with destructive functionality 
might not have its URL actions secured to keep the code shorter.
</div>

Code:

	<div class="warning" markdown='1'>
	...
	</div>

See [Markdown Extra Documentation](http://michelf.com/projects/php-markdown/extra/#html) for more restriction
on placing HTML blocks inside Markdown.

### Further reading

* [Writing great documentation (jacobian.org)](http://jacobian.org/writing/great-documentation/)
* [How tech writing sucks: Five Sins](http://www.slash7.com/articles/2006/11/15/tech-writing-the-five-sins)
* [What is good documentation?](http://www.techscribe.co.uk/techw/whatis.htm)