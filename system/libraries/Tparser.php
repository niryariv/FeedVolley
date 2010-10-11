<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2006, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Parser Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Parser
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/parser.html
 */
class TParser {

	var $l_delim = '{';
	var $r_delim = '}';
	var $object;
		
	/**
	 *  Parse a template
	 *
	 * Parses pseudo-variables contained in the specified template,
	 * replacing them with the data in the second param
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @param	bool
	 * @return	string
	 */
	
	function parse($template, $data)
	{
		//$CI =& get_instance();
		// $template = $CI->load->view($template, $data, TRUE); 
		// unlike the original parser, here the actual template text is passed in $template
		
		if ($template == '')
		{
			return FALSE;
		}
		

		uksort ($data, array($this, '_sort_data'));
				
		foreach ($data as $key => $val)
		{
			if (is_array($val))
			{
				$template = $this->_parse_pair($key, $val, $template);
			}
			else
			{
				$template = $this->_parse_single($key, (string)$val, $template);
				$template = $this->_parse_single_inline($key, (string)$val, $template);
			}
		}
		
		return $template;
	}
	
	
	// This sorts $data so that arrays (pairs) are ahead of strings (singles)
	// This is in order to stop Tparser from replacing all {Title} with the main page title etc
	// Ugly hack for simple parser
	function _sort_data($a, $b) {
		$x = $y = 0;
		
		if (is_array($a)) $x = 1;
		if (is_array($b)) $y = 1;
		
		return ($x-$y);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 *  Set the left/right variable delimiters
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	void
	 */
	function set_delimiters($l = '{', $r = '}')
	{
		$this->l_delim = $l;
		$this->r_delim = $r;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 *  Parse a single key/value
	 *
	 * @access	private
	 * @param	string
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	function _parse_single($key, $val, $string)
	{
		return str_replace($this->l_delim.$key.$this->r_delim, $val, $string);
	}
	
	// Same as _parse_single, but returns the string in one line. (Useful for YAML etc)
	function _parse_single_inline($key, $val, $string)
	{
	  $val = str_replace("\n", ' ', $val);  
		return str_replace($this->l_delim.$key.'.inline'.$this->r_delim, $val, $string);
	}

	// --------------------------------------------------------------------
	
	/**
	 *  Parse a tag pair
	 *
	 * Parses tag pairs:  {some_tag} string... {/some_tag}
	 *
	 * @access	private
	 * @param	string
	 * @param	array
	 * @param	string
	 * @return	string
	 */
	function _parse_pair($variable, $data, $string)
	{	
		if (FALSE === ($match = $this->_match_pair($string, $variable)))
		{
			return $string;
		}

		$str = '';
		foreach ($data as $row)
		{
			$temp = $match['1'];

			foreach ($row as $key => $val)
			{
				if ( ! is_array($val))
				{
					$temp = $this->_parse_single($key, $val, $temp);
					$temp = $this->_parse_single_inline($key, $val, $temp);
				}
				else
				{
					$temp = $this->_parse_pair($key, $val, $temp);
				}
			}
			
			$str .= $temp;
		}
		
		return str_replace($match['0'], $str, $string);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 *  Matches a variable pair
	 *
	 * @access	private
	 * @param	string
	 * @param	string
	 * @return	mixed
	 */
	function _match_pair($string, $variable)
	{	
		// CI BUG FIX here: (.+) -> (.+?) to match pairs that are on the same line
		if ( ! preg_match("|".$this->l_delim . $variable . $this->r_delim."(.+?)".$this->l_delim . '/' . $variable . $this->r_delim."|s", $string, $match))
		{	
			return FALSE;
		}
		return $match;
	}

}
// END Parser Class
?>