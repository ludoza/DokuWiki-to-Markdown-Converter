<?php
require_once('MarkdownCleanup.php');

class DocuwikiToMarkdownExtra {
	/**
	 * Convert docuwiki syntax to markdown
	 *
	 * Author: Mark Stephens
	 * License: BSD
	 */

	/*
	 * KNOWN BUGS:
	 *
	 * - inline code snippets have other inline transforms applied to the code
	 *   body. It needs to be multi-pass:
	 *   - find inline code and replace with a non-translating unique identifier
	 *   - apply other transforms
	 *   - replace unique identifiers with new markup.
	 */

	// These rules are applied whereever inline styling is permitted
	static $inlineRules = array(
		// Headings
		'/^= (.*) =$/'				=>	array("rewrite" => '###### \1'),
		'/^=([^=]*)=*$/'			=>	array("rewrite" => '###### \1'),
		'/^== (.*) ==$/'			=>	array("rewrite" => '##### \1'),
		'/^==([^=]*)=*$/'			=>	array("rewrite" => '##### \1'),
		'/^=== (.*) ===$/'			=>	array("rewrite" => '#### \1'),
		'/^===([^=]*)=*$/'			=>	array("rewrite" => '#### \1'),
		'/^==== (.*) ====$/'		=>	array("rewrite" => '### \1'),
		'/^====([^=]*)=*$/'			=>	array("rewrite" => '### \1'),
		'/^===== (.*) =====$/'		=>	array("rewrite" => '## \1'),
		'/^=====([^=]*)=*$/'		=>	array("rewrite" => '## \1'),
		'/^====== (.*) ======$/'	=>	array("rewrite" => '# \1'),
		'/^======([^=]*)=*$/'		=>	array("rewrite" => '# \1'),

		// Link syntaxes, most specific first
		'/\[\[.*?\|\{\{.*?\}\}\]\]/U' =>
										array("notice" => "Link with image seen, not handled properly"),
		'/\[\[.*?\#.*?\|.*?\]\]/U'	=>	array("notice" => "Link with segment seen, not handled properly"),
		'/\[\[.*?\>.*?\]\]/U'		=>	array("notice" => "interwiki syntax seen, not handled properly"),
		'/\[\[(.*)\]\]/U'			=>	array("call" => "handleLink"),

		// Inline code.
		'/\<code\>(.*)\<\/code\>/U'	=>	array("rewrite" => '`\1`'),
		'/\<code (.*)\>(.*)\<\/code\>/U'	=>	array("rewrite" => '`\2`{\1}'),

		// Misc checks
		'/^\d*\.\s/'				=>	array("notice" => "Possible numbered list item that is not docuwiki format, not handled"),
		'/^=+\s*.*$/'				=>	array("notice" => "Line starts with an =. Possibly an untranslated heading. Check for = in the heading text"),	
		// <x@y.xom>						email
            
                // extra rules for liquibase wiki
                // remove liquibase.org
                //'/]\(http:\/\/liquibase\.org\/([^\)]*)\)/'	=>	array("rewrite" => '](\1)'),
            
                // add .html and site template variables
                //'/]\(([^\)]*)\)/'				=>	array("rewrite" => ']({{ site.url }}/{{ page.lang }}/\1.html)')
	);

	// Contains the name of current input file being processed.
	var $fileName;

	// Contains the line number of the input file currently being processed.
	var $lineNumber;

	// Used when parsing lists. Has one of three values:
	// ""			not processing a list.
	// "unordered"	processing items in an unordered list
	// "ordered"	processing items in an ordered list
	var $listItemType;

	// Counter for ordered lists.
	var $listItemCount;

	function convert($s) {
		$lines = $this->getLines($s);

		$output = "";
		$lineMode = "text";

		$this->listItemType = "";
		$this->lineNumber = 0;

		foreach ($lines as $line) {
			$this->lineNumber++;

			$prevLineMode = $lineMode;

			// Determine if the line mode is changing
			$tl = trim($line);
			if ($lineMode != "code" && preg_match('/^\<code(|\s([a-zA-Z0-9])*)\>$/U', $tl)) {
				$line = "~~~";
				if ($tl != "<code>") $line .= " {" . substr($tl, 6, -1) . "}";
				$lineMode = "code";
			}
			else if ($lineMode == "code" && $tl == "</code>") {
				$line = "~~~";
				$lineMode = "text";
			}
			else if ($lineMode == "text" && strlen($tl) > 0 &&
				($tl[0] == "^" || $tl[0] == "|")) {
				// first char is a ^ so its the start of a table. In table mode we
				// just accumulate table rows in $table, and render when
				// we switch out of table mode so we can do column widths right.
				$lineMode = "table";
				$table = array();
			}
			else if ($lineMode == "table" && ($tl == "" ||
				($tl[0] != "^" && $tl[0] != "|"))) {
				$lineMode = "text";
			}

			if ($prevLineMode == "table" && $lineMode != "table") {
				$output .= $this->renderTable($table);
			}

			// perform mode-specific translations
			switch ($lineMode) {
				case "text":
					$line = $this->convertInlineMarkup($line);
					$line = $this->convertListItems($line);
					break;
				case "code":
					break;
				case "table":
					// Grab this line, break it up and add it to $table after
					// performing inline transforms on each cell.
					$parts = explode($tl[0], $this->convertInlineMarkup($line));
					for ($i=0; $i < count($parts); $i++)
						$parts[$i] = trim($parts[$i]);
					$table[] = $parts;
					break;
			}

			if ($lineMode != "table") $output .= $line . "\n";
		}
		
		$cleanup = new MarkdownCleanup();
		
		$output = $cleanup->process($output);

		return $output;
	}

	static $underline = "";

	function renderTable($table) {
		// get a very big underline
		if (!self::$underline) for ($i = 0; $i < 100; $i++) self::$underline .= "----------";

		// Calculate maximum columns widths
		$widths = array();
		foreach ($table as $row) {
			for ($i = 0; $i < count($row); $i++) {
				if (!isset($widths[$i])) $widths[$i] = 0;
				if (strlen($row[$i]) > $widths[$i]) $widths[$i] = strlen($row[$i]);
			}
		}

		$s = "";
		$headingRow = true;
		foreach ($table as $row) {
			for ($i = 0; $i < count($row); $i++) {
				if ($i > 0) $s .= " | ";
				$s .= str_pad($row[$i], $widths[$i]);
			}
			$s .= "\n";

			if ($headingRow) {
				// underlines of the length of the column headings
				for ($i = 0; $i < count($row); $i++) {
					if ($i > 0) $s .= " | ";
					$s .= str_pad(substr(self::$underline, 0, strlen($row[$i])), $widths[$i]);
				}
				$s .= "\n";
			}

			$headingRow = false;
		}

		return $s;
	}

	// Perform inline translations.
	function convertInlineMarkup($line) {
		// Apply regexp rules
		foreach (self::$inlineRules as $from => $to) {
			if (isset($to["rewrite"]))
				$line = preg_replace($from, $to["rewrite"], $line);
			if (isset($to["notice"]) && preg_match($from, $line))
				$this->notice($to["notice"]);
			if (isset($to["call"]) && preg_match_all($from, $line, $matches))
				$line = call_user_func_array(array($this, $to["call"]), array($line, $matches));
		}

		return $line;
	}

	// Handle transforming list items:
	// __* text		[unordered list item] =>
	// __-			[ordered list item] =>
	// Doesn't handle nested lists, but will emit a notice.
	function convertListItems($s) {
		if ($s == "") return $s;

		if (substr($s, 0, 2) != "  " && $s[0] != "\t" && trim($s) != "") {
			// Termination condition for a list is that the text is not
			// indented.
			$this->listItemType = "";
			return $s;
		}

		if (substr($s, 0, 3) == "  *") {
			$this->listItemType = "unordered";
			$s = substr($s, 2); // remove leading space

			// force exactly 2 spaces after bullet to make things line up nicely.
			if (substr($s, 1, 1) != " ") $s = "\n* " . substr($s, 1);
			if (substr($s, 2, 1) != " ") $s = "\n*  " . substr($s, 2);
		}
		else if (substr($s, 0, 3) == "  -") {
			if ($this->listItemType != "ordered") $this->listItemCount = 1;
			$this->listItemType = "ordered";
			$s = " " . $this->listItemCount . ". " . substr($s, 3);
			$this->listItemCount++;
		}
		else if (substr($s, 0, 3) == "   ") {
			$t = trim($s);
			if ($t && ($t[0] == "*" || $t[0] == "-"))
				$this->notice("Possible nested indent, which isn't handled");
		}
		else if (substr($s, 0, 2) == "  ") {
			// we're a list, but this line is not the start of a point, so
			// indent it. We indent by 4 spaces, which is required for additional
			// paragraphs in an item in markdown. We only have to add 2, because
			// there are already 2 there.
			$s = "  " . $s;
		}

		return $s;
	}

	// Called by a rule that match links with [[ ]]. $line is the line to munge.
	// $matchArgs are passed from preg_match_all; there are always two entries
	// and $matchArgs[0] is the source link including [[ and ]], and is what
	// we can replace with a link.
	//
	// some variants:
	// - [[http://doc.silverstripe.org/doku.php?id=contributing#reporting_security_issues|contributing guidelines]]
	//   has a # fragment, but part of the URL, not ahead of the URL as specified in docuwiki
	// - [[http://url|{{http://url/file.png}}]] (x1)
	// - [[recipes:forms]]
	// - [[recipes:forms|our form recipes]]
	// - [[tutorial:3-forms#showing_the_poll_results|tutorial:3-forms: Showing the poll results]]
	// - [[directory-structure#module_structure|directory structure guidelines]]
	// - [[:themes:developing]]
	// - [[GSoc:2007:i18n]]
	// - [[:Requirements]]
	// - [[#ComponentSet]]
	// - [[ModelAdmin#searchable_fields]]
	// - [[irc:our weekly core discussions on IRC]]
	// - [[#documentation|documentation]]
	// - [[community run third party websites]]
	// - [[requirements#including_inside_template_files|Includes in Templates]]
	function handleLink($line, $matchArgs) {
		foreach ($matchArgs[0] as $match) {
			$link = substr($match, 2, -2);
			$parts = explode("|", $link);

			if (count($parts) == 1) $replacement = "[" . $parts[0] . "](" . $this->translateInternalLink($parts[0]) . ")";
			else {
				if (strpos($parts[1], "{{")) $this->notice("Image inside link not translated, requires manual editing");
				$replacement = "[" . $parts[1] . "](" . $this->translateInternalLink($parts[0]) . ")";
			}

			$line = str_replace($match, $replacement, $line);
		}

		return $line;
	}

	// Called by rules that match image references with {{ }}
	// Specific cases that we handle are:
	// - {{:file.png|:file.png}}
	// - {{http://something/file.png}}
	// - {{http://something/file.png|display}}
	// - {{tutorial:file.png}}
	function handleImage($line, $matchArgs) {
		foreach ($matchArgs[0] as $match) {
			$link == substr($match, 2, -2);
			$parts = explode("|", $link);

			if (count($parts) == 1) $replacement = "![" . $parts[0] . "](" . $this->translateInternalLink($parts[0]) . ")";
			else $replacement = "![" . $parts[1] . "](" . $this->translateInternalLink($parts[0]) . ")";

			$line = str_replace($match, $replacement, $line);
		}
	}

	// Convert an internal docuwiki link, which is basically some combination
	// of identifiers with ":" separators ("namespaces"), which are really
	// folders. The input is any link. This only alters internal links.
	function translateInternalLink($s) {
		if (substr($s, 0, 5) == "http:" || substr($s, 0, 6) == "https") return $s;
		return str_replace(":", "/", $s);
	}

	function notice($message) {
		echo "Notice: {$this->fileName} (line {$this->lineNumber}): $message\n";
	}

	// Return an array of lines from s, ensuring we handle different end-of-line
	// variations
	function getLines($s) {
		// Ensure that we only have a single \n at the end of each line.
		$s = str_replace("\r\n", "\n", $s);
		$s = str_replace("\r", "\n", $s);
		return explode("\n", $s);
	}

	// Convert a docuwiki file in the input directory and called
	// $filename, and re-created it in the output directory, translated
	// to markdown extra.
	function convertFile($inputFile, $outputFile = null, $flags = 0) {
		$this->fileName = $inputFile;
		$s = file_get_contents($inputFile);
		$s = $this->convert($s);

		if($outputFile) {
			if (file_put_contents($outputFile, $s, $flags) === FALSE)
				echo "Could not write file {$outputFile}\n";
		} 
		
		return $s;
	}
}

?>