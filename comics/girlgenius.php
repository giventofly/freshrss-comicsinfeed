<?php

/** Parse Girl Genius Online feed
 *
 * The feed contents do not contain the comic, load the link and parse the page
 */
function parseGirlGenius($entry) {
	$dom = new DOMDocument;
	$dom->loadHTML($entry->content());
	libxml_use_internal_errors(false);

	$comicURL = $entry->link();
	$comicDOM = new DOMDocument;
	$comicDOM->loadHTMLFile($comicURL, LIBXML_NOWARNING | LIBXML_NOERROR);

	$imageContainer = $comicDOM->getElementById('comicbody');
	if (is_null($imageContainer)) {
		return $entry;
	}

	$imageNode = null;
	$imageNodes = $imageContainer->getElementsByTagName('img');
	$imageCount = $imageNodes->length;
	for ($idx = 0; $idx < $imageCount; $idx++) {
		$candidate = $imageNodes->item($idx);
		if ($candidate->getAttribute('alt') === 'Comic') {
			$imageNode = $candidate;
			break;
		}
	}
	if (is_null($imageNode)) {
		return $entry;
	}
	$imageURL = str_replace('http://', 'https://', $imageNode->getAttribute('src'));

	$image = $dom->createElement('img');
	$image->setAttribute('src', $imageURL);

	$body = $dom->getElementsByTagName('body')->item(0);
  while ($body->hasChildNodes()) {
		$body->removeChild($body->firstChild);
	}
	$body->appendChild($image);
	$entry->_content($dom->saveHTML());

	return $entry;
}
