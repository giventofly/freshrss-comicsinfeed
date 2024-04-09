<?php


/** Parse Monster under the bed feed

  The feed contents don't contain the comic, unlike the other comics, so we need to load the link and then parse the page for the comic:
  
  Feed contents:
  ```
<post-id xmlns="com-wordpress:feed-additions:1">12913</post-id>	</item>
		<item>
		<title>#296 &#8220;Silver &#038; Gold&#8221;</title>
		<link>https://themonsterunderthebed.net/comic/296-silver-gold/</link>
					<comments>https://themonsterunderthebed.net/comic/296-silver-gold/#comments</comments>
		
		<dc:creator><![CDATA[Brandon]]></dc:creator>
		<pubDate>Sun, 03 Dec 2023 22:25:27 +0000</pubDate>
				<guid isPermaLink="false">https://themonsterunderthebed.net/?post_type=comic&#038;p=12795</guid>

					<description><![CDATA[<p><a href="https://themonsterunderthebed.net/comic/296-silver-gold/" rel="bookmark" title="#296 &#8220;Silver &#038; Gold&#8221;"></a>
</p>Here we are touching base once again with the sisters! Next update&#8230;. 🔥🔥🔥🔥 &#160; PATREON]]></description>
										<content:encoded><![CDATA[<p><a href="https://themonsterunderthebed.net/comic/296-silver-gold/" rel="bookmark" title="#296 &#8220;Silver &#038; Gold&#8221;"></a>
</p><p>Here we are touching base once again with the sisters! Next update&#8230;. <img src="https://s.w.org/images/core/emoji/14.0.0/72x72/1f525.png" alt="🔥" class="wp-smiley" style="height: 1em; max-height: 1em;" /><img src="https://s.w.org/images/core/emoji/14.0.0/72x72/1f525.png" alt="🔥" class="wp-smiley" style="height: 1em; max-height: 1em;" /><img src="https://s.w.org/images/core/emoji/14.0.0/72x72/1f525.png" alt="🔥" class="wp-smiley" style="height: 1em; max-height: 1em;" /><img src="https://s.w.org/images/core/emoji/14.0.0/72x72/1f525.png" alt="🔥" class="wp-smiley" style="height: 1em; max-height: 1em;" /></p>
<p>&nbsp;</p>
<p><a href="https://www.patreon.com/themonsterunderthebed">PATREON</a></p>
]]></content:encoded>
					
					<wfw:commentRss>https://themonsterunderthebed.net/comic/296-silver-gold/feed/</wfw:commentRss>
			<slash:comments>49</slash:comments>
		
		
		<post-id xmlns="com-wordpress:feed-additions:1">12795</post-id>	</item>
        ...
  ```
*/
function parseMonsterUnderBed($entry){
  $dom = new DOMDocument;
  $dom->loadHTML($entry->content());
  libxml_use_internal_errors(false);

  // Get link to comic page from RSS Feed Entry..
  $comicUrl = $entry->link();

  // Load & parse comic page for panels.
  $img = parseMonsterUnderBedLink($comicUrl);
  // Inject panel back into RSS Feed Entry.
  $image = $dom->createElement('img');
  $image->setAttribute('src', $img);
  $body = $dom->getElementsByTagName('body')->item(0);
  //clear body
  while ($body->hasChildNodes()) {
    $body->removeChild($body->firstChild);
  }
  $body->appendChild($image);
  $entry->_content($dom->saveHTML());
  return $entry;
}

//parse comic panels from comic link.
function parseMonsterUnderBedLink(string $comicUrl): string {
  $dom = new DOMDocument;
  $dom->loadHTMLFile($comicUrl, LIBXML_NOWARNING | LIBXML_NOERROR);
  $comicClass = $dom->getElementById('comic');
  if (is_null($comicClass))
    return $comicUrl;

  $comicElement = $comicClass->getElementsByTagName('img')->item(0);
  return $comicElement->getAttribute('src');
}
