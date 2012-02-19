# Liquibase DokuWiki to Markdown Converter

## Introduction

Tools to convert the DokuWiki syntax to Markdown syntax. Please note it has some specific Liquibase regular expressions in `scripts/DocuwikiToMarkdownExtra.php`.

## Usage

*Note: The following scripts should no longer be used, as they were designed for an earlier stage in the project.*

### Import DokuWiki files

I don't have a idea how DokuWiki store its files so I'm leaving this note here if it might help.

Note: This is only possible by SilverStripe staff. 

	scp -P 2222 -r <username>@doc.silverstripe.org:/sites/ss2doc/www/assets/data/pages/* input/

### Convert to Markdown files

	cd scripts
	php TranslateSSDocs.php ../input <../output> < template.txt>

`../output` - if output is not supplied it will print the conversion to `stdout`.

`template.txt` - at the moment it only prepends the content of the template to each file it converts. 

## Credit

This project was shamelessly forked from the [SilverStripe Documentation Restructuring Project](https://github.com/chillu/silverstripe-doc-restructuring) and was hacked/broken apart to add some extra functionality.