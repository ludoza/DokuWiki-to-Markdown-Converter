# DokuWiki to Markdown Converter

## Introduction

Tools to convert the DokuWiki syntax to Markdown syntax.    
Please note it has some specific Liquibase regular expressions in `scripts/DocuwikiToMarkdownExtra.php`.

## Usage

### Import an entire wiki

	scp -P 2222 -r <username>@myserver:/sites/mysite/www/assets/data/pages/* input/

### Convert to Markdown files

	cd scripts
	php TranslateSSDocs.php ../input <../output> < template.txt>

`../output` - if output is not supplied it will print the conversion to `stdout`.

`template.txt` - at the moment it only prepends the content of the template to each file it converts. 

## Credit

This project was forked from the [SilverStripe Documentation Restructuring Project](https://github.com/chillu/silverstripe-doc-restructuring) 
and was edited to add some extra functionality by [ludoza](https://github.com/ludoza).    
It has subsequently been forked from [ludoza's fork](https://github.com/ludoza/Liquibase-DokuWiki-to-Markdown-Converter) with the
aim of creating a common way to export dokuwiki files to markdown (which is what we need in our 2013 restructuring here
at title.dk).
