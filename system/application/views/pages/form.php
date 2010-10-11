<?php require_once APPPATH . 'views/layouts/header.php'; ?>


<div id="intro">
  <h2>Feedvolley allows you to display any RSS/Atom content with your own HTML. Just fill this form (or <a href="/content/faq">learn more</a>):</h2>
	<!-- h2>Feedvolley allows you to create themable pages for your favorite RSS feeds. No signups, no fuss, and it only takes a few seconds. Get started below (or <a href="/content/faq">learn more</a>)</h2 -->
	
</div>


<? if ($error): ?> <tt><?= $error ?></tt> <? endif ?>


<div id="big_form">
	<form action="/pages/create" method="post">

		<div>
			<label>Feed URL<br /><span>This can be RSS, Atom, or any HTML page that has a feed attached, e.g. "nytimes.com"</span></label><br />
			<input class="field" type="text" size="40" name="feed_url" value="<?= @$vars['feed_url'] ?>" />
		</div>
		
		<div>
			<span class="plus">+</span>
		</div>
		
		<div>
			<label>Your email address<br /><span>To send you an edit link. No spam and your email will not be displayed anywhere.</span></label><br />
			<input class="field" type="text" size="20" name="email" value="<?= @$vars['email'] ?>" />
		</div>

		<div>
			<span class="plus">+</span>
		</div>
		
		<div>
		  <label>Theme<br /><span>You can change it or edit the HTML later on</span></label><br />
		  <?php require_once APPPATH . 'views/themes/selector.html'; ?>
		  <script type="text/javascript" charset="utf-8">set_selected(<?= @$vars['theme_id'] ?>)</script>      
		</div>
		
		<div>
			<input class="submit" type="submit" value="Create Page" />
		</div>
		
	</form>
</div>

<div id="recent_pages">
	<h3>Featured pages:</h3>
	<ul>
		<li><a href="/NYminute">New York Minute</a></li>
		<li><a href="/NYTmuseum">NY Times Homepage with Museum Theme</a></li>
		<li><a href="/CNNsunday">CNN.com with Sunday Edition Theme</a></li>
		<li><a href="/recent">Recently created pages</a></li>
	</ul>
</div>


<?php require_once APPPATH . 'views/layouts/footer.php'; ?>