<?php

/** Parse Flipside feed
 *
 * The feed contents do not contain the comic, we have to load the link and
 * parse the page.
*/
function parseFlipside($entry) {
	$dom = new DOMDocument;
	libxml_use_internal_errors(false);

	$comicURL = $entry->link();
	$comicDOM = new DOMDocument;
	$comicDOM->loadHTMLFile($comicURL, LIBXML_NOWARNING | LIBXML_NOERROR);

	$imageContainer = $comicDOM->getElementById('flip_comicpage');
	if (is_null($imageContainer)) {
		return $entry;
	}

	$imageLinkNode = $imageContainer->getElementsByTagName('a')->item(0);
	$imageNode = $imageLinkNode->getElementsByTagName('img')->item(0);
	// The image URL doesn't contain the domain
	$imageURL = 'https://flipside.gushi.org' . $imageNode->getAttribute('src');

	$image = $dom->createElement('img');
	$image->setAttribute('src', $imageURL);

  $dom->append($image);
	$entry->_content($dom->saveHTML());

	return $entry;
}
