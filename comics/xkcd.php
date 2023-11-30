<?php


/**
  Parse xkcd feed.

  The xkcd feed contains the image, but half of the comedy is in the hover-over
  alt text. This parser extracts out the alt text.
*/
function parseXkcd($entry){

  $dom = new DOMDocument;
  $dom->loadHTML($entry->content());
  libxml_use_internal_errors(false);

  $image = $dom->getElementsByTagName('img')[0];
  if (!is_null($image)) {
    $alt = $image->getAttribute('alt');
    $text = $dom->createElement('p', $alt);
    $body = $dom->getElementsByTagName('body')->item(0);
    $body->appendChild($text);
    $entry->_content($dom->saveHTML());
  }

  return $entry;
}

