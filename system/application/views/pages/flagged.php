<? header('Content-type: text/xml;charset=utf-8'); ?>
<?= '<?xml version="1.0"?>' ?>

<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
        <channel>
                <title>Flagged FeedVolley Sites</title>
                <link>http://feedvolley.com/pages/flagged</link>
                <description>Flagged FeedVolley Sites</description>
                <pubDate><?= date('r') ?></pubDate>
                <lastBuildDate><?= date('r') ?></lastBuildDate>
                <language>en-us</language>
				<? foreach ($flagged as $page): ?>
	                <item>
	                        <title><?= $page->name ?></title>
	                        <link>http://feedvolley.com/<?= $page->name ?></link>
	                        <pubDate><?= date('r') ?></pubDate>
							<guid isPermaLink="false"><?= $page->name . $page->flagged ?></guid>
	                        <description>
                                    <![CDATA[
										<a href="http://feedvolley.com/<?= $page->name ?>">http://feedvolley.com/<?= $page->name ?></a>
										 flagged <?= ($page->flagged  == 1) ? 'once' : $page->flagged . ' times' ?>.
                                    ]]>
                            </description>
	                </item>
				<? endforeach ?>
        </channel>
</rss>