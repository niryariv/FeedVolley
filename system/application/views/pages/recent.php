<? header('Content-type: text/xml;charset=utf-8'); ?>
<?= '<?xml version="1.0"?>' ?>

<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
        <channel>
                <title>Recent FeedVolley Pages</title>
                <link>http://feedvolley.com/pages/recent</link>
                <description>Recent FeedVolley Pages</description>
                <pubDate><?= date('r') ?></pubDate>
                <lastBuildDate><?= date('r') ?></lastBuildDate>
                <language>en-us</language>
				<? foreach ($recent_pages as $page): ?>
	                <item>
                    <title><?= htmlentities($page->title) ?></title>
                    <link>http://feedvolley.com/<?= $page->name ?></link>
                      <pubDate><?= date('r', strtotime($page->created_at)) ?></pubDate>
				              <guid isPermaLink="false"><?= $page->name ?></guid>
                    <description>
                      <![CDATA[
                      <a href="/<?= $page->name ?>">"<?= $page->title ?>"</a>
                       with <?= isset($themes[$page->theme_id]->name) ? $themes[$page->theme_id]->name : 'custom' ?> theme
                      ]]>
                      </description>
	                </item>
				<? endforeach ?>
        </channel>
</rss>