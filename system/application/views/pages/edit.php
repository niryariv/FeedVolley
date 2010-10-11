<?php require_once APPPATH . 'views/layouts/header.php'; ?>

<script type="text/javascript" charset="utf-8">

function update_advanced() {
  f = document.forms[0];
  if (f.theme_id.value == 0) {
    $('#advanced_settings').show(); 
    $('#custom_link').css('background-color', '#fee676')
  } else {
    $('#advanced_settings').hide();
    $('#custom_link').css('background-color', '#fdfefe')
  }
} 

</script>


<? if ($error): ?> <tt><?= $error ?></tt> <? endif ?>

<div id="intro">
	<h2>Edit &quot;<?= $name; ?>&quot;</h2>
</div>

<div id="big_form">
	
<form action="/pages/save" method="POST">
	<input type="hidden" name="page_id" value="<?= $page_id ?>" />

	<div>
		<label>URL:</label><br />
		http://feedvolley.com/<input class="field" type="text" size="10" maxlength="20" name="name" value="<?= $name ?>" style="width:135px;" />
	</div>

	<div>
	 	<label>Feed:</label><br />
		<input class="field" type="text" size="50" name="feed_url" maxlength="255" value="<?= $feed_url ?>" />
	</div>

	<div>
	 	<label>Title:</label><br />
		<input class="field" type="text" size="50" name="title" maxlength="255" value="<?= $title ?>" />
	</div>

	<div>
	 	<label>Theme:</label><br />
	 	<?php $mode = 'edit'; require_once APPPATH . 'views/themes/selector.html'; ?>
	 	Or, <a href="javascript:set_selected(0);" id="custom_link">Use Custom HTML</a>
	 	<script type="text/javascript" charset="utf-8">set_selected(<?= $theme_id ?>)</script>
	</div>

  <label for="reset_html" class="normal">
	  <input type="checkbox" name="reset_html" id="reset_html" unchecked /> Reset custom HTML to selected theme
	</label>
  <br/><br/>
  
	<input type="button" value="save changes" onclick="d = document.forms[0]; d.action = '/pages/save'; d.submit();"/> <br /><br />
	
	
	
	<!-- h4 class="advanced"><a href="javascript:void(0);" onclick="$('#advanced_settings').show();$(this).hide();">Show advanced settings</a></h4 -->
	
	<div id="advanced_settings" style="display:none;">

    	<div class="bar">
    		&mdash;
    	</div>


	 	<label>Custom HTML:</label>
    <a href="javascript:void(0);" onclick="$('#help').toggle();">(Help)</a></h4>
		
		<div id="help" style="text-align:left; display:none;">
  	  <?php require_once APPPATH . 'views/themes/markup_help.html'; ?>  		
    </div>
  	
		<textarea rows="50" cols="100" name="html"><?= $html ?></textarea>

<!--
 		<br /><br />
		<label for="reset_html" class="normal">
		  <input type="checkbox" name="reset_html" id="reset_html" unchecked /> Reset custom HTML to existing theme:
		</label>
		<select name="reset_theme_id">
			<? foreach ($themes as $t): ?>
			<option value="<?= $t->id ?>"
				<?= ($theme_id == $t->id) ? 'selected' : '' ?>>
				<?= (isset($t->name)) ? $t->name : '' ?></option>
			<? endforeach ?>
		</select> 
-->  	
		<br /><br />

  	<input type="button" value="save changes" onclick="d = document.forms[0]; d.action = '/pages/save'; d.submit();"/> <br /><br />

	  <br /><br /><br />
	
<!-- 
    <div class="bar">
     &mdash;
    </div>


		<h4 class="advanced">Export Theme</h4>

		Export your custom HTML as a theme (so that you & others could use it to build other pages).<br /><br />

		New theme's name: <input type="text" size="20" name="theme_name" value="<?= @$theme_name ?>" /> &nbsp;&nbsp;&nbsp;&nbsp;
		<input type="button" value="export theme" onclick="d = document.forms[0]; d.action = '/themes/export'; d.submit();"/> <br /><br />
-->
	</div>


</form>

</div>
<script type="text/javascript" charset="utf-8">update_advanced();</script>

<?php require_once APPPATH . 'views/layouts/footer.php'; ?>
