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

  // TODO: get link to comic page from RSS Feed Entry..
  $comicUrl = 'https://www.penny-arcade.com/comic/2023/11/29/steam-dork';
  // Load & parse comic page for panels.
  $panels = parseComicPanelLinks($comicUrl);
  // Inject panels back into RSS Feed Entry.
  foreach($panels as $panel) {
      // TODO: Do I need to convert panel back into an xml image attribute?
      $entry->_content($panel->ownerDocument->saveHTML($dom));
  }


  // $xpath = new DOMXpath($dom);

  // $pattern = '/assets\.penny-arcade\.com\/comics/panels/(.+)\.jpg/i';
  // $image = $xpath->query("//img");

  // if (!is_null($image)) {
  //   $image = $image->item(0);
  //   if (!is_null($image)) {
  //     $source = $image->getAttribute('src');
  //     if (preg_match($pattern, $source, $matches)) {
  //       // $image->removeAttribute('width');
  //       // $image->removeAttribute('height');
  //       // $pattern = '/(-\d+x\d+)\.(jpg|png|gif)$/';
  //       // $modifiedString = preg_replace($pattern, '.$2', $source);
  //       // $image->setAttribute('src', $modifiedString);
  //       $entry->_content($image->ownerDocument->saveHTML($dom));
  //     }
  //   }
  // }

  // var_dump($entry);
  return $entry;
}


//parse comic panels from comic page.
function parseComicPanelLinks(string $comicUrl): array {
  $dom = new DOMDocument;
  $dom->loadHTMLFile($comicUrl, LIBXML_NOWARNING | LIBXML_NOERROR);
  // var_dump($dom);
  $imgs = array();
  $comicPanelsXml = $dom->getElementById('comic-panels');
  foreach($comicPanelsXml->getElementsByTagName('img') as $img) {
      $imgs[] = $img->getAttribute('src');
  }
  print "panels: ";
  var_dump($imgs);
  return $imgs;
}

// parsePennyArcade('https://www.penny-arcade.com/feed');
parseComicPanelLinks('https://www.penny-arcade.com/comic/2023/11/29/steam-dork');
