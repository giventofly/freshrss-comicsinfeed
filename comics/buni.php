<?php


//parse buni feed
function parseBuni($entry){

  $dom = new DOMDocument;
  $dom->loadHTML($entry->content());
  libxml_use_internal_errors(false);

  $xpath = new DOMXpath($dom);

  /*
  <img width="150" height="150" src="https://www.bunicomic.com/wp-content/uploads/2023/08/2023-08-28-Buni-150x150.jpg" alt="" data-sanitized-class="attachment-thumbnail size-thumbnail wp-post-image">
  
  remove fixed width and heigh, and remove size from end of url image

  */
  $pattern = '/bunicomic\.com\/(.+)(-\d+x\d+)\.(jpg|png|gif)/i';
  $image = $xpath->query("//img");

  if (!is_null($image)) {
    $image = $image->item(0);
    if (!is_null($image)) {
      $source = $image->getAttribute('src');
      if (preg_match($pattern, $source, $matches)) {
        $image->removeAttribute('width');
        $image->removeAttribute('height');
        $pattern = '/(-\d+x\d+)\.(jpg|png|gif)$/';
        $modifiedString = preg_replace($pattern, '.$2', $source);
        $image->setAttribute('src', $modifiedString);
        $entry->_content($image->ownerDocument->saveHTML($dom));
      }
    }
  }


  return $entry;

}

