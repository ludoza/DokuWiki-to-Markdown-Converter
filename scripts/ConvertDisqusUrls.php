<?php
/**
 * Usage: php ConvertDisqusUrls.php <xml-path>
 * 
 * @see http://docs.disqus.com/help/39/
 */
$xmlpath = $_SERVER['argv'][1];
$xml = new SimpleXMLElement(file_get_contents(realpath($xmlpath)));
$threads = $xml->xpath('//thread');
$urls = array();
foreach($threads as $thread) {
	$oldUrl = (string)$thread->link;
	$newUrl = preg_replace('/http\:\/\/doc\.silverstripe\.(com|org)/', 'en/sapphire/reference', $oldUrl);
	$newUrl = str_replace('doku.php?id=', '', $newUrl);
	$urls[$oldUrl] = $newUrl;
}
// $csv = "";
// foreach($urls as $oldUrl => $newUrl) {
// 	$csv .= "$oldUrl,$newUrl\n";
// }
// echo $csv;

$htaccess = "";
foreach($urls as $oldUrl => $newUrl) {
	$oldUrlRelative = preg_replace('/http\:\/\/doc\.silverstripe\.(com|org)\/(doku.php\?id\=)/', '', $oldUrl);
	$htaccess .= sprintf(
		'RewriteRule ^%s$ %s [R=301,L]',
		$oldUrlRelative,
		'http://doc.silverstripe.org/' . $newUrl
	) . "\n";
}
echo $htaccess;