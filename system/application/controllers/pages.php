<?php
class Pages extends Controller {
  
	function index() {
		$s = $this->uri->segment_array();
    
		if (count($s) == 0)
			redirect ('pages/create');
    
		return ($this->_view($s[1]));
	}

	function _view($page_name) { // this is only used internally
	  
		$page = $this->Page->get_by_name($page_name);
		if (!$page)
			return ($this->_form("Unknown page: $page_name"));

		$feed = $this->Feed->get($page->feed_id);
		$feed_url = $feed->feed_url;      

		// allow dynamic feed URLs
    if ($feed->id == 0) {
      if ($feed_url = $this->input->post('ext_feed_url'))
        set_cookie(array('name' => 'ext_feed_url', 'value' => $feed_url, 'expire' => $this->config->item('cookie_lifetime')));
      elseif (!($feed_url = $this->input->cookie('ext_feed_url')))
        $feed_url = 'http://feedvolley.com/recent';
    }
    
		$html = $this->Page->get_html($page->id);
		$rss  = $this->Feed->load($feed_url);
    
		$this->load->library('Tparser');
		$this->load->library('ThemeParser');

		$this->load->helper('edit_bar');
		$output = $this->themeparser->render($rss, $html, $page->title);
		
		// insert edit/duplicate bar
		$output = preg_replace('/(<body.*?>)/msi', '$1 ' . edit_bar($page, $this->User->has_code($page->admin_code)), $output);
		$output = preg_replace('/(<\/body.*?>)/msi',  google_analytics() . '$1' , $output);
		
		// if local request --- this is currently pointless until a better is_local() is created
		if (is_local() && stripos($output, '<body>')===false)
		  $output = edit_bar($page, $this->User->has_code($page->admin_code)) . $output;
		
  //	echo $output;
  // using a view, to enable caching
		$data['content'] = $output;
		$this->load->view ('pages/render_page', $data);
		if (!$this->User->has_code($page->admin_code))
		  $this->output->cache(3);
	}

	function _form($error = false, $params = array()) {
		foreach($this->Theme->homepage_get_all() as $r) 	
		  $data['themes'][$r->id] = $r;
		
		$data['error'] = $error;

		if (empty($params['theme_id']))
		  $params['theme_id'] = $this->config->item('default_theme_id');
		$data['vars'] = $params;
		  
		$this->load->view ('pages/form', $data);
	}


	function create() {
		if ($_SERVER['REQUEST_METHOD'] == 'GET') return $this->_form();
		
		$feed_url = trim($_POST['feed_url']);		
		$email 		= trim($_POST['email']);

		$theme_id = (int) $_POST['theme_id'];
		if ($theme_id == 0) $theme_id = $this->config->item('default_theme_id');
				
		$name     = $this->Page->make_unique_name();
			
		$params = array('feed_url' => $feed_url, 'email' => $email, 'theme_id' => $theme_id);
		
		if ($feed_url == '') 
			return ($this->_form("Please enter feed URL", $params));
			
	 	if (false === ($f = $this->Feed->get_or_create($feed_url)))
			return ($this->_form("Can't find RSS/Atom feed on '$feed_url'", $params));

		$feed_id = $f->id;
		$title = $f->name;
		
		if ($new = $this->Page->create ($feed_id, $theme_id, $email, $name, null, $title)) {
			$this->User->register($new);
			redirect($new['name']); // SUCCESS
		} else {
			return ($this->_form($this->Page->validation_error, $params));
		}
	}
	
	function dup() {
		$email 	= $_POST['email'];
		$page_id= (int) $_POST['page_id'];
		
		$name = $this->Page->make_unique_name();
		
		if (!$this->Page->is_unique_name($name)) die ("$name already used");
		if (!valid_email($email)) die ('Invalid email');
		
		if (!($p = $this->Page->get($page_id))) die ("cant find page to duplicate");
		
		$html = ($p->theme_id == 0 ? $p->html : null); // dont copy past "customize html" if the theme isn't customize (to prevent confusion)
		
		if ($new = $this->Page->create ($p->feed_id, $p->theme_id, $email, $name, $html, $p->title)) {
			$this->User->register($new);
			redirect($new['name']); // SUCCESS
		} else die ("can't create page");
	}
	
	function flag() {
		$page_id = $_POST['page_id'];
		if (!$this->Page->flag($page_id))
			die ("cannot flag page");
		else
			echo ('Page flagged. Thanks!<br><a href="/">Home</a>');
	}
	
	
	function edit($admin_code = false, $error = false) {
		if (!$admin_code) $admin_code = $this->uri->segment(3);
		
		$page = $this->Page->get_by_admin_code($admin_code);
		if ($page)
			$feed = $this->Feed->get($page->feed_id);
		
		if (!$page) die ("Not authorized to edit this or page doesn't exist");
		
		$data['themes']	 = $this->Theme->get_all();
		
		$data['html'] 	 = $page->html;
		if ($data['html'] == '') $data['html'] = $this->Page->get_html($page->id);

		$data['page_id'] = $page->id;		
		$data['feed_url']= $feed->feed_url;
		$data['theme_id']= $page->theme_id;
		$data['name']	   = $page->name;
		$data['title']   = $page->title;
    
		$data['error']	 = $error;
		
		$this->load->view('pages/edit', $data);
	}
	
	function save() { // for edit()
		$page_id = (int) $_POST['page_id'];
		$theme_id= (int) $_POST['theme_id'];
		$feed_url= (string) substr(trim ($_POST['feed_url']), 0, 254); // hard limit to 255 chars
		$title   = (string) substr(trim ($_POST['title']), 0, 254); 
		$name    = (string) substr(trim ($_POST['name']), 0, 19);

    // dont save custom HTML unless the theme is "Custom HTML" (to prevent confusion)
		$html = ($theme_id == 0 ? $_POST['html'] : null);

		
		$reset_html = isset($_POST['reset_html']);
		
		if (!($page = $this->Page->get($page_id)))
			die ("Unknown Page ID");
		
		$old_name = $page->name;
		
		if ($reset_html) {
			if (!($t = $this->Theme->get($theme_id))) 
			  die ("NO THEME ID"); // roughly handled since this means action was called manually
			$html = $t->html;
		}
		
		if ($p = $this->Page->save_changes($page_id, $html, $feed_url, $theme_id, $name, $title)) {
			if ($name != $old_name) $this->Page->add_name($page_id, $old_name);
			redirect($p->name);
		}
		// failed to save
		return ($this->edit($page->admin_code, $this->Page->validation_error));
	}
	
	function flagged() {
		$flagged = $this->Page->get_flagged();
		$data['flagged'] = $flagged;
		
		$this->load->view('pages/flagged', $data);
	}
	
  function email_check()
	{
		$this->load->library('validation');
		$email = $this->input->post('email', true);
		if (valid_email($email))
			echo '<span style="color: #a3d869;">Okay &rarr;</span>';
		else
			echo '<span style="color: #f53a3a;">Invalid &rarr;</span>';
	}
	
	// show recently created pages
	function recent() {
		$data['themes'][0] = '';
		foreach($this->Theme->homepage_get_all() as $r) 	
		  $data['themes'][$r->id] = $r;
		
	  $data['recent_pages'] = $this->Page->get_recent();
		$this->load->view('pages/recent', $data);
	}  
	
}
?>