<?

# insert into feeds(id, name, feed_url, html_url, homepage_worthy) values (0, '', '{url}', '', 0);

class Feed extends Model {
	
	function Feed() {
		parent::Model();
	}
	
	function _clear_cache_file($feed_url) {
	    $cache_dir = $this->config->item('simplepie_cache');
	    @unlink("$cache_dir/".md5($feed_url).".spc");
	}
	
	
	function load($feed_url, $retry = false) {
		$this->load->library('simplepie');
		
		$f = new SimplePie();
        $f->set_cache_duration($this->config->item('simplepie_cache_lifetime'));
        $f->cache_location = $this->config->item('simplepie_cache');
		$f->set_feed_url($feed_url);
		$f->init();
		$f->handle_content_type();
		
        // SimplePie tends to have cache issues, so clear the cache and try one more time
        // garbage code for garbage environment
		if ($f->error)
		    if ($retry)
		        return false;
		    else {
		        $this->_clear_cache_file($feed_url);
		        $this->load($feed_url, true);
		    }
		else 
			return $f;
	}
	
	function get_or_create($feed_url) {
	  
		if ($f = $this->get_by_feed_url($feed_url))
			return $f;
			
		if ($this->create($feed_url))
			return $this->get_by_feed_url($feed_url);
		else
			return false;
	}
	
	function create($feed_url) {
			
		if (!($f = @$this->load($feed_url)))
			return false;
		
		$data = array( 
				'homepage_worthy' => 1,
				'name'	=> $f->get_title(),
				'feed_url'	=> $feed_url,
				'html_url'	=> $f->get_link(),
				'created_at'=> date( 'Y-m-d H:i:s' )
			 	);

		if ($this->db->insert('feeds', $data))
			return $data;
		else 
			return false;
	}
	
	function get($id) {
		$q = $this->db->get_where('feeds',  array('id' => $id));
		return array_pop($q->result());
	}
	
	function get_all() {
		$q = $this->db->get('feeds');
		return $q->result();
	}

	function homepage_get_all() {
		$q = $this->db->get_where('feeds',  array('homepage_worthy' => 1));
		return $q->result();
	}
	
	function get_by_feed_url($feed_url) {		
		$q = $this->db->get_where('feeds', array('feed_url' => trim($feed_url)));
		return array_pop($q->result());
	}

}
?>