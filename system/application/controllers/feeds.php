<?

// This is unused right now, but I keep it cause it would be useful for AJAX (when replacing sessions with echo)
class Feeds extends Controller {
	
	function add() {
		$this->load->library('session');
		
		$url  = trim ($_POST['url']);
		$name = trim ($_POST['name']);

		$this->session->set_flashdata('feed', true);
		
		if ($f = $this->Feed->get_by_feed_url($url)) {
			$this->session->set_flashdata('exists', $url);
			redirect('');
		}

		if ($add = $this->Feed->create($url, $name)) {
			$this->session->set_flashdata('added', $add);			
		} else {
			$this->session->set_flashdata('fail', $url);			
		}

		redirect('');
		
	}

}
?>
