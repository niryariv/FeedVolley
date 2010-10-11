<?

class Page extends Model {
	
	var $validation_error;
	
	function Page() {
		parent::Model();
	}
	
	function validate($action, $feed_id, $theme_id, $email, $name, $page_id = false) {
		$error = false;
		
		if 		  (!$this->Feed->get($feed_id)) 	  $error = 'Feed not found';
		elseif 	($theme_id != 0 && !$this->Theme->get($theme_id))   $error = 'Theme not found';

		elseif 	($name == '')	$error = 'Missing name';
    elseif  (!$this->validation->alpha_dash($name))
      $error = "Name can only contain letters, numbers and '-' or '_' characters";
    
	  elseif  (!$this->is_unique_name($name)) {
	    if (!$action == 'create')    # error if it's a 'create' action...
        $error = 'Name already used';
	    elseif (!($p = $this->get_by_name($name)) || $p->id != $page_id)
	      $error = 'Name already used';	  #... or 'update' but the name is not the page's own name
		}
				
		elseif 	($action == 'create' && !valid_email($email)) $error = 'Invalid email';
				
		if (!$error) return true;
		
		$this->validation_error = $error;
		return false;
	}
	
	function create($feed_id, $theme_id, $email, $name = false, $html = null, $title = false) {
		
		if (!$name)   $name = $this->make_unique_name();		
		
		if (!$this->validate('create', $feed_id, $theme_id, $email, $name))
			return false;
			
		$data = array(
					'homepage_worthy' => 1,			
					'author_email' => $email,
					'feed_id'	=> 	$feed_id,
					'theme_id'	=>	$theme_id,
					'name'		=>  $name,  // URL name
					'title'   => $title,  // page title
					'admin_code'=>	md5(uniqid()),
					'created_at'=> date( 'Y-m-d H:i:s' ),
					'html'		=> $html
					);
					
		if ($this->db->insert('pages', $data))
			return $data;
		else 
			return false;
	}


	function save_changes($id, $html, $feed_url, $theme_id, $name, $title) {
		if (!($feed = $this->Feed->get_or_create($feed_url))) {
			$this->validation_error = "Cannot load feed $feed_url";
			return false;
		}
			
		if (!($page = $this->get($id))) {
			$this->validation_error = "Page ID doesnt exist";
			return false;
		}
		
		$changes = array('feed_id' => $feed->id, 'theme_id' => $theme_id, 'name' => $name, 'title' => $title);
		if ($html != null) $changes['html'] = $html; 
		
		if (!$this->validate('update', $feed->id, $theme_id, false, $name, $id) ||
			!$this->db->update('pages', $changes, "id = $id"))
				return false;

		return ($this->get($id));
	}
	
	function add_name ($id, $name) {
		return ($this->db->insert('page_names', array('page_id' => $id, 'name' => $name)));
	}


	function get($id) {
		$q = $this->db->get_where('pages', array('id' => $id));
		return array_pop($q->result());
	}
	
	function get_by_name($name) {
		$q = $this->db->get_where('pages', array('name' => $name));
		$page = array_pop($q->result());
		if (!is_null($page)) return $page;
		
		$q = $this->db->get_where('page_names', array('name' => $name));
		$n = array_pop($q->result());
		if (is_null($n)) return $n;
		
		return $this->get($n->page_id);
	}

	function get_by_admin_code($admin_code) {
		$q = $this->db->get_where('pages', array('admin_code' => $admin_code));
		return array_pop($q->result());
	}
	
	function get_recent($limit = 20) {
		$q = $this->db->query("SELECT DISTINCT theme_id, feed_id, name, title, created_at FROM pages WHERE homepage_worthy = 1 ORDER BY created_at DESC LIMIT $limit");
		return $q->result();
	}

	function get_html($page_id) {
		$p = $this->get($page_id);

		if ($p->theme_id == 0) 
			return $p->html;

		$t = $this->Theme->get($p->theme_id);
		
		return $t->html;
	}
	

	function is_unique_name($name) {
		return is_null($this->get_by_name($name));
	}

	function make_unique_name() {
		$id = time() * microtime();
		$name = base_convert($id, 10, 36);
		
		if (!$this->is_unique_name($name))		// in the unlikely case..
			return strtolower(md5(uniqid()));
		
		return $name;
	}
	

	function flag($page_id) {
		if (!($p = $this->get($page_id)))
			return false;

		$f = (int) ++$p->flagged;
		return $this->db->update('pages', array('flagged' => $f), "id = $page_id");
	}
	
	function get_flagged() {
		$q = $this->db->query('SELECT id, name, flagged FROM pages WHERE flagged > 0');
		return $q->result();
	}

}

?>