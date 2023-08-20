<?php

//parse buttersafe feed
function parseButterSafe($entry){

  $dom = new DOMDocument;
  $dom->loadHTML($entry->content());
  libxml_use_internal_errors(false);

  $xpath = new DOMXpath($dom);

  $pattern = '/(buttersafe\.com\/comics\/rss\/)(.+)(RSS\.(jpg|png|gif))/i';
  //http://www.buttersafe.com/comics/rss/2023-03-30-RunningRSS.jpg should be https://www.buttersafe.com/comics/2023-03-30-Running.jpg

  $image = $xpath->query("//img");

  if (!is_null($image)) {
      $image = $image->item(0);
      if (!is_null($image)) {
          $source = $image->getAttribute('src');
          if (preg_match($pattern, $source, $matches)) {
              $replacement = 'buttersafe.com/comics/$2.$4';
              $modifiedString = preg_replace($pattern, $replacement, $source);
              $image->setAttribute('src', $modifiedString);
              $entry->_content($image->ownerDocument->saveHTML($dom));
          }
      }
  }

  return $entry;

}