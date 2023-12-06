<?php


/** Parse Penny-Arcade feed

  The feed contents don't contain the comic, unlike the other comics, so we
  need to load the link and then parse the page for the comics: Feed contents:

  ```
  <?xml version="1.0" encoding="UTF-8"?>
  <rss xmlns:atom="http://www.w3.org/2005/Atom" version="2.0">
      <channel>
          <title>Penny Arcade</title>
          <link>https://www.penny-arcade.com/</link>
          <description>Penny Arcade comics and news.</description>
          <atom:link href="https://www.penny-arcade.com/feed" rel="self" type="application/rss+xml" />
          <language>en-us</language>
          <copyright>Copyright (c) 1998-2023 Penny Arcade, Inc.</copyright>
          <lastBuildDate>Wed, 29 Nov 2023 03:23:23 +0000</lastBuildDate>
          <item>
              <title>Steam Dork</title>
              <link>https://www.penny-arcade.com/comic/2023/11/29/steam-dork</link>
              <description>New Comic: Steam Dork</description>
              <pubDate>Wed, 29 Nov 2023 08:01:00 +0000</pubDate>
              <guid isPermaLink="true">https://www.penny-arcade.com/comic/2023/11/29/steam-dork</guid>
      </item>
      ...
  ```

  Once we have loaded the page, we need to then extract the comic panels.
  Penny-Arcade has split the comic into 3 panels, so that they can render the
  comic horizontally on desktop or vertically on phones. eg. panels:

  ```
  ...
  <div class="comic-area">
      <!-- New standard panel size -->
      <a id="comic-panels" class="three-panel" >
          <div class="comic-panel"><img src="https://assets.penny-arcade.com/comics/panels/20231129-Ysr69e72-p1.jpg" srcset="https://assets.penny-arcade.com/comics/panels/20231129-Ysr69e72-p1.jpg 540w,https://assets.penny-arcade.com/comics/panels/20231129-Ysr69e72-p1@2x.jpg 1080w" alt=""></div>
          <div class="comic-panel"><img src="https://assets.penny-arcade.com/comics/panels/20231129-Ysr69e72-p2.jpg" srcset="https://assets.penny-arcade.com/comics/panels/20231129-Ysr69e72-p2.jpg 540w,https://assets.penny-arcade.com/comics/panels/20231129-Ysr69e72-p2@2x.jpg 1080w" alt=""></div>
          <div class="comic-panel"><img src="https://assets.penny-arcade.com/comics/panels/20231129-Ysr69e72-p3.jpg" srcset="https://assets.penny-arcade.com/comics/panels/20231129-Ysr69e72-p3.jpg 540w,https://assets.penny-arcade.com/comics/panels/20231129-Ysr69e72-p3@2x.jpg 1080w" alt=""></div>
      </a>
  ...
  ```

  Once we have the 3 panels, we can strip the `@2x` for the double-sized image.
*/
function parsePennyArcade($entry){
  $dom = new DOMDocument;
  $dom->loadHTML($entry->content());
  libxml_use_internal_errors(false);

  // Get link to comic page from RSS Feed Entry..
  $comicUrl = $entry->link();

  if (str_contains($comicUrl, 'news')) {
      // Early return on news entries!
      return $entry;
  }

  // Load & parse comic page for panels.
  $imgLinks = parseComicPanelLinks($comicUrl);
  // Inject panels back into RSS Feed Entry.
  foreach($imgLinks as $img) {
      $image = $dom->createElement('img');
      $image->setAttribute('src', $img);
      $body = $dom->getElementsByTagName('body')->item(0);
      $body->appendChild($image);
  }
  $entry->_content($dom->saveHTML());
  return $entry;
}


//parse comic panels from comic page.
function parseComicPanelLinks(string $comicUrl): array {
  $htmlContent = bypassGDPRAccept($comicUrl, 'reject');
  $imgLinks = scrapeImgLinks($htmlContent);
  return $imgLinks;
}


// Function to bypass the JavaScript GDPR prompt using cURL.
function bypassGDPRAccept($url, $cookieType = 'reject') {
  // Set the cookie type based on the parameter
  $cookieValue = '';
  if ($cookieType === 'reject') {
    $cookieValue = "gdpr[consent_types]=%7B%22necessary%22%3Atrue%7D";
  } elseif ($cookieType === 'necessaryonly') {
    $cookieValue = "gdpr[consent_types]=%7B%22necessary%22%3Atrue%2C%22preferences%22%3Afalse%2C%22statistics%22%3Afalse%2C%22marketing%22%3Afalse%7D";
  }

  // Make a cURL request to load the URL and bypass the prompt
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_COOKIE, $cookieValue);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $output = curl_exec($ch);
  curl_close($ch);

  return $output;
}

// Function to scrape Comic Panel img links from HTML using DOMDocument.
function scrapeImgLinks($html) {
  $dom = new DOMDocument;
  @$dom->loadHTML($html);
  $comicPanelsClass = $dom->getElementById('comic-panels');
  $imgTags = $comicPanelsClass->getElementsByTagName('img');
  $imgLinks = [];

  foreach ($imgTags as $imgTag) {
    $imgLinks[] = $imgTag->getAttribute('src');
  }

  return $imgLinks;
}
