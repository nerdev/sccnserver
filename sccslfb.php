<?php
// sccac.php
// Tyler Holmgren
// Modified: 11/16/14
header("Content-type: text/xml");
    // Set Timezone
    date_default_timezone_set('America/Chicago');
    // Confirmed events schema
    $confirmed = 'http://schemas.google.com/g/2005#event.confirmed';
    // Time as of right now (when the script was launched)
    $right_now = date("Y-m-d\Th:i:sP", time());
    $pubdate = date( "r", time() );
    $two_weeks_in_seconds = 2 * (60 * 60 * 24 * 8);
    $two_weeks = date("Y-m-d\Th:i:sP", time() + $two_weeks_in_seconds);
    // This is the feed I am using for the academic calendar
    $feed = "https://www.google.com/calendar/feeds/s377a9ptglfvjsk5tcqgem3fkg%40group.calendar.google.com/" .
        "public/full?orderby=starttime&singleevents=true&" .
        "sortorder=ascending&" .
	"start-min=" . $right_now . "&" .
        "start-max=" . $two_weeks;
    // Create a new document to hold the rss feed
    $doc = new DOMDocument();
    $doc->load( $feed );
    $entries = $doc->getElementsByTagName("entry");
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
          <rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\">
          <channel>
          <title>SCC Student Life Faribault</title>\n";
    echo "<atom:link href=\"http://sccnserver-tylerjholmgren.rhcloud.com/sccac.php\" rel=\"self\" type=\"application/rss+xml\" />\n";
    echo "<description>SCC Student Life Faribault</description>
          <language>en</language>
          <webMaster>tylerholmgren@tjh.pw (Tyler Holmgren)</webMaster>
          <category>Academic</category>";
    echo "<pubDate>$pubdate</pubDate>
          <lastBuildDate>$pubdate</lastBuildDate>";
    echo "<ttl>60</ttl>";
    // Dynamicly create rss posts!
    foreach ( $entries as $entry ) {
        $status = $entry->getElementsByTagName("eventStatus");
        $eventStatus = $status->item(0)->getAttributeNode("value")->value;
        if ($eventStatus == $confirmed) {
          $titles = $entry->getElementsByTagName("title");
          $title = $titles->item(0)->nodeValue;
          $title = str_replace(" & ", " &amp; ", $title);
          $times = $entry->getElementsByTagName( "when" );
          $startTime = $times->item(0)->getAttributeNode("startTime")->value;
  	      $when = date( "l\, F j\, Y \a\\t h:i A T", strtotime( $startTime ) );
          $web = $entry->getElementsByTagName( "link" );
          $link = $web->item(0)->getAttributeNode("href")->value;
  	      $contents = $entry->getElementsByTagName("content");
  	      $content = $contents->item(0)->nodeValue;
          $content = str_replace(" & ", " &amp; ", $content);
  	      $whereabouts = $entry->getElementsByTagName("where");
  	      $where = $whereabouts->item(0)->getAttributeNode("valueString")->value;
              echo "<item>\n";
              echo "<title>$title</title>\n";
              echo "<link>$link</link>\n";
  	          echo "<date>Starts: $when</date>\n";
              echo "<description>Where: $where<br/>Description: $content</description>\n";
              echo "<guid isPermaLink=\"true\">$link</guid>\n";
              echo "</item>\n\n";
	     }
    }
echo "</channel>
      </rss>";
?>
