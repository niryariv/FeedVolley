<?

class Theme extends Model {
		
	function Theme() {
		parent::Model();
	}
	
	function create($html, $name) {
		
		$data = array( 
				'homepage_worthy' => 1,			
				'name'		=> $name,
				'html'		=> $html,
				'created_at'=> date( 'Y-m-d H:i:s' ),
				'hash'		=> $this->_html_to_hash($html)
			 );

		if ($this->db->insert('themes', $data))
			return $data;
		else
			return false;
	}
	
	function _html_to_hash($html) {
		return md5($html);
	}
	
	function get($id) {
		$q = $this->db->get_where('themes',  array('id' => $id));
		return array_pop($q->result());
	}

	function get_all() {
		$q = $this->db->get_where('themes', 'id <> 0');
		return $q->result();
	}

	 function get_by_html($html) {
	 	$hash = $this->_html_to_hash($html);
	 	$q = $this->db->get_where('themes',  array('hash' => $hash));
	 	if (count($q->result()) > 0) 
	 		return array_pop($q->result);
	 	else
	 		return false;
	 }
	
	function homepage_get_all() {
		$q = $this->db->get_where('themes',  array('homepage_worthy' => 1));
		return $q->result();
	}



	function is_unique_name($name){
		$q = $this->db->get_where('themes',  array('LCASE(name)' => strtolower($name)));
		return (count($q->result()) == 0);
	}
		
}
?>
