<?php

/** Parse Bouletcorp feed

  The feed contents don't contain the comic, it is needed to:
  - load the link
  - then parse the page for the comic images (multiple images possible)
  - add the title as title and alt text

  Feed contents:
  ```
  <?xml version="1.0" encoding="utf-8"?>
  <rss version="2.0">
    <channel>
      <title>bouletcorp.com</title>
      <link>https://bouletcorp.com</link>
      <description>Bouletcorp, le site web de Boulet.</description>
      <lastBuildDate>Mon, 14 Oct 2024 00:00:00 GMT</lastBuildDate>
      <docs>https://validator.w3.org/feed/docs/rss2.html</docs>
      <generator>https://github.com/nuxt-community/feed-module</generator>
      <language>fr</language>
      <copyright>2023</copyright>
      <category>Comics</category>
      <category>Comics</category>
      <item>
        <title>Entretien avec un Vampire 04</title>
        <link>https://bouletcorp.com/rogatons/2024/10/14</link>
        <guid>283</guid>
        <pubDate>Mon, 14 Oct 2024 00:00:00 GMT</pubDate>
        <description>Entretien avec un Vampire 04</description>
        <enclosure url="https://bouletcorp.com/uploads/033_Vampire02_970e145d47.jpg" length="0" type="image/jpg"/>
      </item>
      ...
  ```
*/

function parseBouletcorp($entry) {
  $dom = new DOMDocument;
  $dom->loadHTML($entry->content());
  libxml_use_internal_errors(false);

  // Get link to comic page from RSS Feed Entry
  $comicUrl = $entry->link();

  // Load & parse comic page for all panels
  $images = parseBouletcorpLink($comicUrl);

  // Inject panels back into RSS Feed Entry
  foreach ($images as $imgData) {
    $image = $dom->createElement('img');
    $image->setAttribute('src', $imgData['src']);
    $image->setAttribute('title', $imgData['title']);
    $image->setAttribute('alt', $imgData['title']);
    $body = $dom->getElementsByTagName('body')->item(0);
    $body->appendChild($image);
  }

  $entry->_content($dom->saveHTML());
  return $entry;
}

// Parse all comic panels from comic link
function parseBouletcorpLink(string $comicUrl): array {
  $dom = new DOMDocument;
  $dom->loadHTMLFile($comicUrl, LIBXML_NOWARNING | LIBXML_NOERROR);

  // Find the article with id "comic-strip"
  $comicStrip = $dom->getElementById('comic-strip');
  $images = [];

  // Ensure the "comic-strip" element exists
  if (!is_null($comicStrip)) {
    // Get all img elements within this section
    $imgElements = $comicStrip->getElementsByTagName('img');
    foreach ($imgElements as $img) {
      $images[] = [
        'src' => $img->getAttribute('src'),
        'title' => $img->getAttribute('title') ?? '',  // Retrieve title, default to empty if missing
      ];
    }
  }

  return $images;  // Return array of image URLs
}

