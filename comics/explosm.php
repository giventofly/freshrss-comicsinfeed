<?php


/** Parse Explosm feed

  The feed contents don't contain the comic, unlike the other comics, so we
  need to load the link and then parse the page for the comic:
  
  Feed contents:
  ```
  <?xml version="1.0" encoding="utf-8"?>
  <rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>Explosm.net</title>
        <link>https://explosm.net</link>
        <description>Daily Comics and more!</description>
        <lastBuildDate>Sun, 10 Mar 2024 00:00:11 GMT</lastBuildDate>
        <docs>https://validator.w3.org/feed/docs/rss2.html</docs>
        <generator>Explosm.net</generator>
        <language>en</language>
        <copyright>Copyright 2024, Explosm.net</copyright>
        <atom:link href="https://explosm.net/rss" rel="self" type="application/rss+xml"/>
        <item>
            <title><![CDATA[Comic for 2024.03.10 - Gaylight Savings]]></title>
            <link>https://explosm.net/comics/gaylight-savings</link>
            <guid>https://explosm.net/comics/gaylight-savings</guid>
            <pubDate>Sun, 10 Mar 2024 00:00:11 GMT</pubDate>
            <description><![CDATA[New Cyanide and Happiness Comic]]></description>
            <category>Comics</category>
        </item>
        ...
  ```
*/
function parseExplosm($entry){
  $dom = new DOMDocument;
  $dom->loadHTML($entry->content());
  libxml_use_internal_errors(false);

  // Get link to comic page from RSS Feed Entry..
  $comicUrl = $entry->link();

  // Load & parse comic page for panels.
  $img = parseExplosmLink($comicUrl);
  // Inject panel back into RSS Feed Entry.
  $image = $dom->createElement('img');
  $image->setAttribute('src', $img);
  $body = $dom->getElementsByTagName('body')->item(0);
  $body->appendChild($image);
  $entry->_content($dom->saveHTML());
  return $entry;
}

//parse comic panels from comic link.
function parseExplosmLink(string $comicUrl): string {
  $dom = new DOMDocument;
  $dom->loadHTMLFile($comicUrl, LIBXML_NOWARNING | LIBXML_NOERROR);
  $comicClass = $dom->getElementById('comic');
  if (is_null($comicClass))
    return $comicUrl;

  $comicElement = $comicClass->getElementsByTagName('img')->item(0);
  return $comicElement->getAttribute('src');
}
