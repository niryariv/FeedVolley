<?

class Themes extends Controller {

	function Themes() {
		parent::Controller();
	}
	
	// for AJAX checks
	function is_unique_name(){
		$name = $_POST['name'];
		if ($this->Theme->is_unique_name($name))
			echo "OK";
		else
			echo "NOT";
	}
	
	
	function export() {
	  die(); // export is disabled, for now

		$name = trim($_POST['theme_name']);
		$page_id = $_POST['page_id'];
		$html = $_POST['html'];
		
		$page = $this->Page->get($page_id);
		
		if ($name == '' || !$this->Theme->is_unique_name($name))
		 	redirect('pages/edit/'.$page->admin_code);

		//echo ("name: $name ; page_id = $page_id");
		$this->Theme->create($html, $name);
		redirect('/'.$page->name);
		
	}
	
	
	function add($error = false) {
		$data['error'] = $error;
		$this->load->view('themes/add', $data);
	}
	
	// **** uncomment to enable /themes/add in order to add themes to the DB ****
  // function create() {
  //  if ($_SERVER['REQUEST_METHOD'] == 'GET') return $this->add();
  //  
  //  $name = trim($_POST['name']);
  //  $url  = trim($_POST['url']);
  // 
  //  if ($name == '') return ($this->add('Missing name'));
  // 
  //  if (!$this->Theme->is_unique_name($name)) return ($this->add('Name already used'));
  //  
  //  if ($url == '') {
  //    if ($this->do_upload()) {
  //      $udata = $this->upload->data();
  //      $html = file_get_contents($udata['full_path']);
  //    } else {
  //      return($this->add($this->upload->display_errors()));
  //    }
  //  } elseif (!($html = file_get_contents(prep_url($url)))) {
  //    return($this->add("Cant open URL: $url"));
  //  }
  //  
  //  if ($t = $this->Theme->get_by_html($html))
  //    return($this->add("This theme already exists: $t->name"));
  //  
  //  if ($this->Theme->create($html, $name))
  //    echo "Added!";
  //  else
  //    echo "Failed to store theme in DB";
  // }
  //  
  // function do_upload() {
  //  $config['upload_path'] = './uploads/'; // <CI root>/uploads
  //  $config['allowed_types'] = 'txt|html';
  //  $config['max_size'] = '100'; // KB
  // 
  //  $this->load->library('upload', $config);
  //  return $this->upload->do_upload();
  //  
  // }
	
	function help() {
	  $this->load->view('themes/custom_html.html');
	}
		
}

?>