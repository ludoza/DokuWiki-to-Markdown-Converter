<?php
/**
 * Fixes certain omissions from {@link DocuwikiToMarkdownExtra}.
 * 
 * @author Ingo Schommer
 */
class MarkdownCleanup {
	
	function processFile($filepath) {
		$content = $this->process(file_get_contents($filepath));
		$content = $this->relocateImages($content, $filepath);
		
		return $content;
	}
	
	function process($content) {
		$content = $this->convertInlineHTML($content);
		$content = $this->convertUnbalancedHeadlines($content);
		$content = $this->convertCodeBlocks($content);
		$content = $this->newlinesAfterHeadlines($content);
		$content = $this->newlinesBeforeLists($content);
		$content = $this->convertApiLinks($content);
		$content = $this->convertEmphasis($content);
		// TODO Fixed width messes up tables, breaks up links, etc.
		// $content = $this->fixedWidth($content);
		
		return $content;
	}
	
	protected function convertInlineHTML($content) {
		$out = array();
		
		$lines = $this->getLines($content);
		foreach($lines as $i => $line) {
			// TODO Don't convert HTML in headlines
			if(!preg_match('/^\t/', $line)) {
				$lines[$i] = preg_replace('/[\*\'`]*(<[^>]*?>)[\*\'`]*/', '`$1`', $lines[$i]);
			}
			
			$out[] = $lines[$i];
		}
		
		return implode("\n", $out);
	}
	
	/**
	 * Convert all image references from dokuwiki format into markdown,
	 * and relocate the physical files (they were all stored in one folder regarless
	 * of the markdown file location). Creates copies of images in case they're referenced
	 * from multiple places, to avoid breaking already converted paths.
	 * 
	 * Note: Doesn't add image heights from DokuWiki (e.g. image.png?100 makes it 100px wide).
	 * 
	 * # Example
	 * 
	 * Before: {{tutorial:home-first.png|My Title}}
	 * After: ![My Title](home-first.png)
	 */
	protected function relocateImages($content, $filepath) {
		$origImgFolder = realpath('../master/cms/docs/en/reference/_images/media/');
		$targetImgFolder = dirname($filepath) . '/images/';
		
		// create images folder
		if(!file_exists($targetImgFolder)) mkdir($targetImgFolder);
		
		preg_match_all('/\{\{\s*(.*?)\\s*}\}/m', $content, $matches);
		if($matches) foreach($matches[1] as $i => $match) {
			// var_dump($match);
			// split into path (with optional namespaces) and optional title
			$specParts = explode('|', $match);
			$dokuwikiPath = $specParts[0];
			$title = (isset($specParts[1])) ? $specParts[1] : '';
			$title = preg_replace('/^:/', '', $title); // Remove trailing colon (root namespace)
			
			// Don't rewrite absolute URLs (no need to copy images either then)
			$parsed = parse_url($dokuwikiPath);
			if(isset($parsed['scheme']) && $parsed['scheme'] == 'http') {
				$targetImgHref = $dokuwikiPath;
			} else {
				$dokuwikiPath = preg_replace('/^:/', '', $dokuwikiPath); // Remove trailing colon (root namespace)
				$dokuwikiPathParts = explode(':', $dokuwikiPath);
				$filename = $dokuwikiPathParts[count($dokuwikiPathParts)-1];
				$filename = preg_replace('/\?.*/', '', $filename); // remove querystrings from filename
				$origImgPath = $origImgFolder . '/' . implode('/', (array)$dokuwikiPathParts);
				$targetImgPath = $targetImgFolder . '/' . $filename;
				$origImgPath = preg_replace('/\?.*/', '', $origImgPath); // remove querystrings from filename
				
				// Unset title if its the same as the filename
				if($title == $filename) $title = '';

				// Copy the image file
				if(file_exists($origImgPath)) {
					copy($origImgPath, $targetImgPath);
					// shell_exec("git add $targetImgFolder");
					// shell_exec("git mv $origImgPath $targetImgPath");
				} else {
					echo sprintf('Original image not found: %s' . "\n", $origImgPath);
				}
				
				$targetImgHref = 'images/' . $filename;
			}
			
			// Change to Markdown syntax (see http://daringfireball.net/projects/markdown/syntax#img)
			$content = str_replace(
				$matches[0][$i], 
				sprintf('![%s](%s)', $title, $targetImgHref),
				$content
			);
			
		}
		
		return $content;
	}
	
	// /**
	//  * Put everything thats not in a code block into a fixed width of 120 characters.
	//  * Its important that this runs after {@link convertCodeBlocks()}.
	//  */
	// protected function fixedWidth($content) {
	// 	$out = array();
	// 	
	// 	$lines = $this->getLines($content);
	// 	foreach($lines as $i => $line) {
	// 		if(!preg_match('/^\t/', $lines[$i])) {
	// 			$lines[$i] = wordwrap($lines[$i], 120);
	// 		}
	// 		
	// 		$out[] = $lines[$i];
	// 	}
	// 	
	// 	return implode("\n", $out);
	// }
	
	/**
	 * Convert "unbalanced" DokuWiki headlines (amount of equal signs at beginning and end not matching).
	 */
	protected function convertUnbalancedHeadlines($content) {
		$out = array();
		
		$inlineRules = array(
			'/^=([^=]*) [=\s]*/'			=>	array("rewrite" => '###### $1'),
			'/^==([^=]*) [=\s]*/'			=>	array("rewrite" => '##### $1'),
			'/^===([^=]*) [=\s]*/'			=>	array("rewrite" => '#### $1'),
			'/^====([^=]*) [=\s]*/'			=>	array("rewrite" => '### $1'),
			'/^=====([^=]*) [=\s]*/'		=>	array("rewrite" => '## $1'),
			'/^======([^=]*) [=\s]*/'		=>	array("rewrite" => '# $1'),
		);
		$lines = $this->getLines($content);
		foreach($lines as $i => $line) {
			foreach($inlineRules as $rule => $replace) {
				$lines[$i] = preg_replace($rule, $replace['rewrite'], $lines[$i]);
			}
			$out[] = $lines[$i];
		}
		
		return implode("\n", $out);
	}
	
	/**
	 * Replace emphasis in format "//emphasized//" to "*emphasized*", but avoid replacing it
	 * withing links. E.g. "[http://bla.com](http://bla.com)" shouldn't match, either should
	 * "Convert http:// to https://".
	 */
	protected function convertEmphasis($content) {
		$out = array();
		
		$lines = $this->getLines($content);
		foreach($lines as $i => $line) {
			// Mandate space before tags to avoid converting protocol links
			$lines[$i] = preg_replace('/\s\/\/([^\s][^\]]*?)\/\//', ' *$1*', $lines[$i]);
			// Fix tags without space at start, but at file start
			$lines[$i] = preg_replace('/^\/\/([^\s][^\]]*?)\/\//', '*$1*', $lines[$i]);
			$out[] = $lines[$i];
		}
		
		return implode("\n", $out);
	}
	
	/**
	 * Exchange any links to api.ss.org with a new pseudo format "[api:<classname>]".
	 * Also wrap them in <pre> blocks.
	 * 
	 * Excludes composite structures with spaces etc, we can't be sure they're class names.
	 */
	protected function convertApiLinks($content) {
		$out = array();
		
		$lines = $this->getLines($content);
		foreach($lines as $i => $line) {
			preg_replace('/\[(\w*)\]\(http:\/\/api\.silverstripe.org.*\)/', '`[api:$1]`', $lines[$i]);
			$out[] = $lines[$i];
		}
		
		return implode("\n", $out);
	}
	
	/**
	 * Headlines should be followed by newlines in markdown, for easier readability.
	 */
	protected function newlinesBeforeLists($content) {
		$out = array();
		$re = '/^[\s\t]*\*/';
		
		$lines = $this->getLines($content);
		foreach($lines as $i => $line) {
			if(preg_match($re, $lines[$i]) && isset($lines[$i-1]) && !empty($lines[$i-1]) && !preg_match($re, $lines[$i-1])) {
				$lines[$i] = "\n" . $lines[$i];
			}
			
			$out[] = $lines[$i];
		}
		
		return implode("\n", $out);
	}
	
	/**
	 * Headlines should be followed by newlines in markdown, for easier readability.
	 */
	protected function newlinesAfterHeadlines($content) {
		$out = array();
		
		$lines = $this->getLines($content);
		foreach($lines as $i => $line) {
			if(preg_match('/^#/', $lines[$i]) && isset($lines[$i+1]) && !empty($lines[$i+1])) {
				$lines[$i+1] = "\n" . $lines[$i+1];
			}
			
			$out[] = $lines[$i];
		}
		
		return implode("\n", $out);
	}
	
	/**
	 * Convert Markdown Extra style code blocks with triple tildes
	 * into more standard tab-indented code blocks with CodeHilite convetions.
	 */
	protected function convertCodeBlocks($content) {
		$out = array();
		$linemode = 'text'; // 'text' or 'code'
		$extraPreNewline = false;
		
		$lines = $this->getLines($content);
		foreach($lines as $i => $line) {
			if(preg_match('/^~~~(\s{(.*)})?/', $lines[$i], $matches)) {
				// first line of code block
				if($linemode == 'text') {
					$linemode = 'code';

					// add code formatting bit
					$lines[$i] = (isset($matches[2])) ? ":::" . $matches[2] : '';
					
					// if previous line is not empty, add a newline
					$extraPreNewline = (isset($lines[$i-1]) && !empty($lines[$i-1]));
				} else {
					// last line of code block
					$linemode = 'text';
					$lines[$i] = ''; // remove line with closing tildes
				}
			} 
			
			// HACK Add tabs if starts with ':::' (shouldnt be necessary)
			if($linemode == 'code' || preg_match('/^\n?:::/msi', $lines[$i])) $lines[$i] = "\t" . $lines[$i];
			
			if($extraPreNewline) $lines[$i] = "\n" . $lines[$i];
			$extraPreNewline = false;
			
			$out[] = $lines[$i];
		}
		
		return implode("\n", $out);
	}
	
	protected function getLines($s) {
		// Ensure that we only have a single \n at the end of each line.
		$s = str_replace("\r\n", "\n", $s);
		$s = str_replace("\r", "\n", $s);
		return explode("\n", $s);
	}
	
}

// TODO Disabled in order to run file-by-file as class on DocuWikiToMarkdownExtra.php

// $inputDirectory = "../master/";
// 
// $cleanup = new MarkdownCleanup();
// 
// $path = realpath($inputDirectory);
// 
// $objects = new RecursiveIteratorIterator(
//                new RecursiveDirectoryIterator($path), 
//                RecursiveIteratorIterator::SELF_FIRST);
// 
// foreach($objects as $name => $object) {
// 	$filename = $object->getFilename();
// 	$ext = pathinfo($filename, PATHINFO_EXTENSION);
// 	if ($filename == "." || $filename == ".." || $ext != 'md') continue;
// 	
// 	$inputDir = $object->getPath();
// 	if (is_dir($object->getPathname())) continue;
// 
// 	$newContent = $cleanup->process("{$inputDir}/{$filename}");
// 	file_put_contents("{$inputDir}/{$filename}", $newContent);
// }
