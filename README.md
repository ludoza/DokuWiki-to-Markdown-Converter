# SilverStripe Documentation Restructuring Project

This is a temporary repository for developer documentation about the SilverStripe framework, currently located at [http://doc.silverstripe.org].

*Note: This repository is used for collaboration during the restructuring phase, and will move elsewhere afterwards*

 * [Mailinglist](http://groups.google.com/group/silverstripe-documentation)
 * [Planning wiki page](http://doc.silverstripe.org/tmp:documentation-restructuring)
 * [TODO list and bugtracker](http://open.silverstripe.org/query?status=inprogress&status=new&status=replyneeded&status=reviewed&component=Documentation&order=priority&col=id&col=summary&col=status&col=type&col=priority&col=milestone&col=component)

## Contact

 * Documentation questions: Ingo Schommer (ingo at silverstripe dot com)
 * Markdown Conversion: Mark Stephens (mark at silverstripe dot com)

## Contributing

### Requirements

You'll need to [install git](http://help.github.com/git-installation-redirect), which is quite straightforward on most operating systems.

To edit the files, you just need a text editor. They're written in [Markdown](http://daringfireball.net/projects/markdown/), which many editors have syntax highlighting for (e.g. through the [Textmate plugin](http://github.com/textmate/markdown.tmbundle)).

To preview changes, you will need to run a local webserver capable of running SilverStripe.  [XAMPP](http://www.apachefriends.org/en/xampp.html) is a good option.

### Folders

 * `master`: The new markdown files - please edit in here
 * `scripts`: Helper scripts for conversion and file management

### Contributing

#### Forking the project into your github account

You can **fork** this project within github, as described on [help.github.com](http://help.github.com/forking/).
This fork will show up on your own github profile, and can be checked out to your local machine.
You don't need a webserver on there, or run any project-specific scripts. 

#### Updating your project fork

You should update your own repository every now and then from "upstream" (the original repository),
to ensure you make any changes on the most recent version of files.
See "[Pulling in upstream changes](http://help.github.com/forking/#pulling_in_upstream_changes)" on help.github.com.

#### Committing changes back through "pull requests"

See "[Pull requests](http://help.github.com/pull-requests/)" on help.github.com.

#### Merging in "pull requests"

This only needs to be done by maintainers of the original repository.
Merging back *pull requests* can
either be done [straight on github](http://github.com/chillu/silverstripe-doc-restructuring/forkqueue) (for simple cases), or through `git pull` commands (see [guide](http://github.com/guides/pull-requests)).

### Documentation Guidelines

Its important to read the [documentation guidelines](http://github.com/chillu/silverstripe-doc-restructuring/blob/master/master/sapphire/docs/en/misc/contributing.md), in order to keep a consistent
writing and formatting style. 
We use a customized version of [Markdown](http://daringfireball.net/projects/markdown/syntax) and [Markdown Extra](http://michelf.com/projects/php-markdown/extra/) called "[ss-markdown](http://github.com/chillu/silverstripe-doc-restructuring/blob/master/master/sapphire/docs/en/misc/ss-markdown.md)"

### Previewing your changes

The preview/ directory contains a SilverStripe site that you can visit to preview what the documentation site will look like.  You will need to create a `_ss_environment.php` file in the root of your project or in the directory above.

Here is a minimal one.  Alter the database settings to suit your set-up.

    <?php
    define('SS_ENVIRONMENT_TYPE', 'dev');
    define('SS_DATABASE_SERVER', 'localhost');
    define('SS_DATABASE_USERNAME', 'root');
    define('SS_DATABASE_PASSWORD', '');

If you have check out silverstripe-doc-restructuring into your webroot, you should be able to visit a URL such as http://localhost/silverstripe-doc-restructuring/preview/ to preview your changes.

To add your own module preview, add the following to `preview/mysite/_config.php`:

	DocumentationService::register("mymodule", realpath("../../master/mymodule/docs/"));

### Contributing your changes

After **pushing your changes** to your own github fork, you can send us **pull requests**.

## Obsolete actions

The following scripts should no longer be used, as they were designed for an earlier stage in the project.

### Import DokuWiki files

Note: This is only possible by SilverStripe staff.

	scp -P 2222 -r <username>@doc.silverstripe.org:/sites/ss2doc/www/assets/data/pages/* input/

### Convert to Markdown files

	cd scripts
	php TranslateSSDocs.php ../input ../output