<?

function google_analytics() {
  $a = <<<CODE
    <script type="text/javascript">
    var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
    document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
    </script>
    <script type="text/javascript">
    var pageTracker = _gat._getTracker("UA-991158-3");
    pageTracker._initData();
    pageTracker._trackPageview();
    </script>
CODE;

  return $a;
}

function is_local(){
  // need to fix
  return false;
}

function edit_bar($page, $has_code = false) {
	$html = '<div id="feedvolley_edit_bar">'
		  .	( $has_code ? '<a href="pages/edit/'. $page->admin_code .'"><b>Customize</b></a>  &bull; ' : '')
			.	'<a href="#" onclick="ajaxCheckEmail();$(\'#feedvolley_edit_bar\').hide();$(\'#feedvolley_edit_bar_form\').show();return false;">Create a page like this</a>'
			.	' &bull; <a href="http://feedvolley.com/">Powered by Feedvolley</a>';
			
	$html .= <<<HTML
	&bull;
	<a id="flag_page" href="/pages/flag" 
	 onclick="if (confirm('Flag this page as abusive?')) { var f = document.createElement('form'); this.parentNode.appendChild(f); f.method = 'POST'; f.action = this.href; var i = document.createElement('input'); i.type='hidden'; i.name='page_id'; i.value='$page->id'; f.appendChild(i); f.submit(); };return false;"
	><img src="/assets/images/flag_icon.png" alt="Flag this page" title="Flag this page" /></a>
	</div>
	
	<div id="feedvolley_edit_bar_form" style="display: none;">
		<form action="/pages/dup" method="post" onsubmit="return checkEmail();">
			<input type="hidden" name="page_id" value="$page->id">
			<!-- New URL: feedvolley.com/<input type="text" name="name" /> -->
			<span id="email_check">
			</span>
			Email: <input type="text" name="email" id="email" onchange="ajaxCheckEmail();" onkeyup="ajaxCheckEmail();" />
			<input type="submit" value="Create!" />
			<input type="button" value="Cancel" onclick="$('#feedvolley_edit_bar').show();$('#feedvolley_edit_bar_form').hide();return false;">
		</form>
	</div>
	<script type="text/javascript" charset="utf-8" src="/assets/js/jquery-1.2.3.pack.js"></script>
	<script type="text/javascript" charset="utf-8">
		function checkEmail()
		{
			var email = $('#email');
			if (!email.val() || !email.val().match(/\@/))
			{
				alert('Please enter a valid email address.');
				email.focus();
				return false;
			}
		}
		
		function ajaxCheckEmail()
		{
			var email = $('#email');
			if (email.val())
			{
				$('#email_check').load('/pages/email_check', {email: email.val()});
			}
		}
	</script>
HTML;
		
	$html .= <<<CSS
<style type="text/css" media="screen">
	#feedvolley_edit_bar,
	#feedvolley_edit_bar_form		{ background: #0d0d0d; color: #fff; text-align: right; padding: 3px; font: 11px Verdana, Helvetica, Arial, sans-serif; }
	#feedvolley_edit_bar a 			{ color: #fed189; border: none; }
	#feedvolley_edit_bar a:hover 	{ color: #ffedd0; }
	#feedvolley_edit_bar_form input { background: #0d0d0d; color: #fff; }
	#feedvolley_edit_bar a#flag_page img { border: none; vertical-align: middle; }
</style>
CSS;
	
	return $html;
}
?>