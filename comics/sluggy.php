<?php

/** Parse Sluggy Freelance feed
 *
 * The feed does not contain the comic, we have to load the page and parse it out.
 */
function parseSluggyFreelance($entry) {
	$dom = new DOMDocument;
	$dom->loadHTML($entry->content());
	libxml_use_internal_errors(false);

	$comicURL = $entry->link();
	$comicDOM = new DOMDocument;
	$comicDOM->loadHTMLFile($comicURL, LIBXML_NOWARNING | LIBXML_NOERROR);

	$imageNode = null;
	$divList = $comicDOM->getElementsByTagName('div');
	$divCount = $divList->length;
	for ($idx = 0; $idx < $divCount; $idx++) {
		$candidate = $divList->item($idx);
		if ($candidate->getAttribute("class") === 'comic_content') {
			$imageNode = $candidate->getElementsByTagName('img')->item(0);
			break;
		}
	}
	if (is_null($imageNode)) {
		return $entry;
	}

	$image = $dom->createElement('img');
	$image->setAttribute('src', $imageNode->getAttribute('src'));

	$body = $dom->getElementsByTagName('body')->item(0);
  // The feed doesn't have any useful content
	while ($body->hasChildNodes()) {
		$body->removeChild($body->firstChild);
	}
	$body->appendChild($image);
	$entry->_content($dom->saveHTML());

	return $entry;
}
