<?php

/** Parse Questionable Content feed
 *
 * The feed does contain the image, but the link is a http link even though
 * the site supports HTTPS. This parser just rewrites the image link to use
 * https://
 */
function parseQuestionableContent($entry) {
	$dom = new DOMDocument;
	$dom->loadHTML($entry->content());
  libxml_use_internal_errors(false);

	$imageNodes = $dom->getElementsByTagName('img');
	if (!is_null($imageNodes)) {
		$imageCount = $imageNodes->length;
		for ($idx = 0; $idx < $imageCount; $idx++) {
			$image = $imageNodes->item($idx);
			$imageURL = str_replace('http://', 'https://', $image->getAttribute('src'));
			$image->setAttribute('src', $imageURL);
		}
	}
	$entry->_content($dom->saveHTML());

	return $entry;
}
