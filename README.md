# SilverStripe Documentation Restructuring Project

Tools to convert the original SilverStripe documentation wiki (based on DokuWiki syntax)
to the new Markdown-driven [doc.silverstripe.org](http://doc.silverstripe.org).

*UPDATE: The conversion has been completed*

 * [Mailinglist](http://groups.google.com/group/silverstripe-documentation)
 * [Planning wiki page](http://doc.silverstripe.org/tmp:documentation-restructuring)
 * [TODO list and bugtracker](http://open.silverstripe.org/query?status=inprogress&status=new&status=replyneeded&status=reviewed&component=Documentation&order=priority&col=id&col=summary&col=status&col=type&col=priority&col=milestone&col=component)

## Contact

 * Documentation questions: Ingo Schommer (ingo at silverstripe dot com)
 * Markdown Conversion: Mark Stephens (mark at silverstripe dot com)

## Usage

*Note: The following scripts should no longer be used, as they were designed for an earlier stage in the project.*

### Import DokuWiki files

Note: This is only possible by SilverStripe staff.

	scp -P 2222 -r <username>@doc.silverstripe.org:/sites/ss2doc/www/assets/data/pages/* input/

### Convert to Markdown files

	cd scripts
	php TranslateSSDocs.php ../input ../output