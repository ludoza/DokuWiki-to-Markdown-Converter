<?php
/**
 * php Translate.php <inputfile|inputdir> <outputdir>
 * 
 * If no <outputdir> is given, writes to stdout.
 * 
 * Converts a file or folder (incl. subdirs) to markdown,
 * and writes files to a new output location.
 * 
 * @author Mark Stephens, Ingo Schommer
 */

$args = @$_SERVER['argv'];
$inputDir = (isset($args[1])) ? realpath($args[1]) : "../input/";
//NOTE (Anselm March 2013): There seems to be an inconsistency in the script, so that when 
//single files are converted, they are always placed in the input dir nomatter what the settings are.
//Thus I just changed the default to true, so they'll alway be placed there anyway
$outputDir = (isset($args[2])) ? realpath($args[2]) : true;

echo "Output Path " , $outputDir,  "\n";
$template = (isset($args[3])) ? file_get_contents(realpath($args[3])) : false;

require_once("DocuwikiToMarkdownExtra.php");

$converter = new DocuwikiToMarkdownExtra();

$path = realpath($inputDir);

// Process either directory or file
if(is_dir($inputDir)) {
	$objects = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator($path), 
		RecursiveIteratorIterator::SELF_FIRST
	);
} else {
	$objects = array(new SplFileInfo($inputDir));
}


foreach($objects as $name => $object) {
	$filename = $object->getFilename();

	if ($filename == "." || $filename == "..") continue;
	
	$inputDir = $object->getPath();
	if (is_dir($object->getPathname())) continue;

	
		
	if($outputDir) {
		// Create output subfolder (optional)
                // Was this here gor recursive code?
		$outputDir = str_replace($path, substr($path, 0, -5) . "output", $inputDir);
            
		if (!file_exists($outputDir)) mkdir($outputDir, 0777, true);
		$outFilename = preg_replace('/\.txt$/', '.md', $filename);
		if ($template) {
			$flags = FILE_APPEND; 
                        echo "Writing file ",  "{$outputDir}/{$outFilename}" , "\n";
			if (file_put_contents("{$outputDir}/{$outFilename}", $template) === FALSE)
				echo "Could not write file {$outputFile}\n";
	
		} else {
			$flags = 0; 
		}
		$converter->convertFile(
			"{$inputDir}/{$filename}",
			"{$outputDir}/{$outFilename}",
			$flags
		);
	} else {
		echo $converter->convertFile(
			"{$inputDir}/{$filename}"
		);
	}
	
}

?>
