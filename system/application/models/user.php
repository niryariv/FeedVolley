<?
// Usage:
//
// $this->User->register($page_id)  -  attaches a user to a page, sending the email & setting the cookie
// $this->has_code($code)           -  checks whether a user has permission to edit a page


class User extends Model {
	
	function User() {
		parent::Model();
		$this->load->helper('cookie');
	}
	
	function register($page) {
		return ($this->_email_code($page['author_email'], $page['admin_code'], $page['name'])
				&& $this->_add_code_to_cookie($page['admin_code']));
	}

	function has_code($code) {
		$c = $this->_get_codes_from_cookie();
		return (strpos($c, "|$code|") !== false);
	}
	
	function _email_code($email, $code, $name) {
		$this->load->library('email');

		$this->email->from('noreply@feedvolley.com', 'Feedvolley.com');
		$this->email->to($email);
		$this->email->subject('Your Feedvolley page: '. $name);

		$this->email->message(
			'Congratulations on your new page at http://feedvolley.com/' . $name . "\n\n".
			'To edit the page, use this URL: http://feedvolley.com/pages/edit/' . $code . "\n\n".
			'Have fun!');

		return $this->email->send();
	}
	
	function _add_code_to_cookie($code) {
		$codes = $this->_get_codes_from_cookie();
		$codes .= "|$code|";
		
		$cookie = array(
		                   'name'   => 'fv_codes',
		                   'value'  => $codes,
		                   'expire' => $this->config->item('cookie_lifetime'),
		                   'domain' => '.feedvolley.com',
		                   'path'   => '/'
		               );

		return set_cookie($cookie);
		
	}
	
	function _get_codes_from_cookie() {
		return get_cookie('fv_codes');
	}

}
?>