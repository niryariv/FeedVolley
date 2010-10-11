<?php

// This automatically displays content pages. 
// To create a content page you only need to create a 'views/content/<page>.php' file. No need to write a new controller method.

class Content extends Controller {

  function index() {
    $this->load->view('layouts/header');

    $s = $this->uri->segment_array();
    if (!isset($s[2]) || !file_exists(BASEPATH.'application/views/content/'.$s[2].'.php'))
      show_404();
    else 
      $this->load->view('content/'.$s[2]);

    $this->load->view('layouts/footer');
  }

}
?>