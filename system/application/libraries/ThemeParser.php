<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

# Fatal error: Call to a member function subscribe_url() on a non-object in /var/www/html/feedvolley/system/application/libraries/ThemeParser.php on line 21

class ThemeParser extends TParser {
		
    function render($feed, $theme, $title) {
    	$this->rss_feed = $feed;
    	$this->page_title = ($title != '') ? $title : $this->rss_feed->get_title();

    	$out = $this->parse($theme, $this->_item_tags());
    	$out = $this->parse($out, $this->_global_tags());
    	$out = $this->parse($out, $this->_parse_css_colors($out));
    	$out = $this->parse($out, $this->_clean_empty_tags());

    	return $out;
    }
  
	function _global_tags() {
		$f = $this->rss_feed;
		
		if (!is_object($f))
		  die ("Cannot access the feed for <b>$this->page_title</b> right now. The feed URL might have changed or the server is slow. Please try again later.");
		  
		$data['Title']	= $this->page_title;
		$data['RSS'] 	= $f->subscribe_url();
		$data['Favicon'] = $f->get_favicon();
		$data['Description'] = $f->get_description();
		
		$data['block:Description'] 	= array ( array('Description' => $f->get_description()));
		$data['block:IndexPage']	  = array ( array('index' => true));
				
		return $data;	
	}
	
	function _item_tags() {
	  if (!is_object($this->rss_feed))
	    die ("Cannot access the feed for <b>$this->page_title</b> right now. The feed URL might have changed or the server is slow. Please try again later.");
	    //return array('block:Posts' => array(0 => array('block:Text' => array(array('Body' => "Can't access the feed for <b>$this->page_title</b> right now. The feed URL might have changed or the server is slow. Please try again later.")))));
	  
        if (!($items = $this->rss_feed->get_items(0,49))) {
            return array();
        }
         
                 
		foreach ($items as $item) 
			$data['block:Posts'][] = $this->_item($item);
    
		return $data;
	}
	
	function _item($item) {
	  
	  if ($cat = $item->get_category())
	    $category = ($cat->get_label() == '') ? $cat->get_term() : $cat->get_label();
	  else
	    $category = '';

    $embed = ($enclosure = $item->get_enclosure()) ? '<embed src="' . html_entity_decode($enclosure->link) . '" autostart="false" />' : false;
    
    $body = $description = $item->get_content();
    
    if ($embed && strpos($item->get_content(), $enclosure->link) === false)
      $body .= $embed; // only if the Enclosure's URL is not in the Description
      
		$post = array (	
					    array (	
					      'Body'	=> $body, 
							  'Description'   => $description,
							  'block:Title' 	=> 
								  array (	
									  array (	
									    'Permalink' => $item->get_permalink(),
											'Title' 	=> $item->get_title()
									  ),
								  ),
					    ),
				    );
		  

		$tags = array(
				'block:Text'	=> $post, 
				'block:Regular'	=> $post,				
        				
				'Permalink' => $item->get_permalink(),
				'Title'		=> $item->get_title(),

	      'Enclosure' => $embed,
	      
	      'Category' => $category,
				
				'block:Photo' => array(),
				'block:Quote' => array(),
				'block:Link'  => array(),
				'block:Chat'  => array(),
				'block:Video' => array(),
				'block:Audio' => array(),
				'block:Conversation' => array(),
				'block:PermalinkPagination' => array(),
				'block:PreviousPost'		=> array(),
				'block:NextPost'			=> array(),
				'block:RebloggedFrom'		=> array()

			);

		$date = $this->_date_parse($item->get_date('U'));
		if ($this->_is_new_day($date)) {
			$tags['block:NewDayDate'] = $post;
			$tags['block:SameDayDate'] = array();
		} else {
			$tags['block:NewDayDate'] = array();
			$tags['block:SameDayDate'] = $post;
		}
			
		$tags = array_merge ($tags, $date);
		return $tags;
	}

	
	function _is_new_day($date) {
		static $current_day = '';

		$day = $date['DayOfYear'].$date['Year'];
		if ($day == $current_day) return false;
		
		$current_day = $day;
		return true;
	}
	
	function _date_parse($timestamp) {
		$t = array('Timestamp' => $timestamp);
		
		list (
			$t['DayOfMonth'], $t['DayOfMonthWithZero'], $t['DayOfWeek'], $t['ShortDayOfWeek'], $t['DayOfWeekNumber'], 
			$t['DayOfMonthSuffix'], $t['DayOfYear'], $t['WeekOfYear'], $t['Month'], $t['ShortMonth'], $t['MonthNumber'], 
			$t['MonthNumberWithZero'], $t['Year'], $t['ShortYear'], $t['AmPm'], $t['CapitalAmPm'], $t['12Hour'], $t['24Hour'], 
			$t['12HourWithZero'], $t['24HourWithZero'], $t['Minutes'], $t['Seconds'], $t['Beats']) 
				= explode ('|', date ('j|d|l|D|w|S|z|W|F|M|n|m|Y|y|a|A|g|h|G|H|i|s|B', $timestamp));
				
		$today = array();
    // list ($cur_year, $cur_mon, $cur_week, $cur_day, $cur_hour, $cur_min, $cur_sec) 
    //    = explode ('|', date ('Y|n|W|j|G|i|s'));
		
		$ago = explode(',', timespan($timestamp, time())); // convert '2 days, 14 hours, 6 minutes' -> '2 days'				
		$t['TimeAgo'] = $ago[0] . ' ago';

		return $t;
		
	}
	
	function _parse_css_colors($theme) {
		if (preg_match_all('/<meta.*name="(color:.*?)".*content="(.*)".*>/i', $theme, $c) == 0) return array();
		
		$names = $c[1]; $codes = $c[2]; $colors = array();
		foreach ($names as $i=>$n) 
			$colors[$names[$i]] = $codes[$i];
		
		return $colors;
	}
	
	function _clean_empty_tags() {
		$tags = $this->_tumblr_tags();
		$data = array();

		foreach ($tags['single'] as $t)
			$data[$t] = '';
			
		foreach ($tags['block'] as $t)
			$data[$t] = array();
		
		return $data;
	}
	

	function _tumblr_tags() {
		
		$single = array ('Title', 'Favicon', 'Description', 'Permalink', 'Body', 'PhotoAlt', 'Caption', 'Quote', 'Source',
					'URL', 'Target', 'Name', 'Alt', 'UserNumber', 'Label', 'Line', 'PreviousPage', 'NextPage', 'RSS', 
					'CustomCSS', 'PostTitle', 'CurrentPage', 'TotalPages', 'PreviousPost', 'NextPost', 'PostID', 
					'TagsAsClasses', 'ReblogParentName', 'ReblogParentTitle', 'ReblogParentURL', 'ReblogRootName', 
					'ReblogRootTitle', 'ReblogRootURL', 'PostAuthorName', 'PostAuthorTitle', 'PostAuthorURL', 
					'GroupMemberName', 'GroupMemberTitle', 'GroupMemberURL', 'LinkURL', 'LinkOpenTag', 'LinkCloseTag', 
					'Length', 'AudioPlayer', 'AudioPlayerWhite', 'AudioPlayerGrey', 'AudioPlayerBlack', 'DayOfMonth', 
					'DayOfMonthWithZero', 'DayOfWeek', 'ShortDayOfWeek', 'DayOfWeekNumber', 'DayOfMonthSuffix', 'DayOfYear', 
					'WeekOfYear', 'Month', 'ShortMonth', 'MonthNumber', 'MonthNumberWithZero', 'Year', 'ShortYear', 'AmPm', 
					'CapitalAmPm', '12Hour', '24Hour', '12HourWithZero', '24HourWithZero', 'Minutes', 'Seconds', 'Beats', 
					'Timestamp', 'TimeAgo', 'PreviousDayPage', 'NextDayPage', 'FollowedName', 'FollowedTitle', 'FollowedURL',
					'PhotoURL-400', 'Video-400', 'PortraitURL-16', 'PortraitURL-24', 'PortraitURL-30', 'PortraitURL-40', 
					'PortraitURL-48', 'PortraitURL-64', 'PortraitURL-96', 'PortraitURL-128', 'ReblogParentPortraitURL-16', 
					'ReblogParentPortraitURL-24', 'ReblogParentPortraitURL-30', 'ReblogParentPortraitURL-40', 
					'ReblogParentPortraitURL-48', 'ReblogParentPortraitURL-64', 'ReblogParentPortraitURL-96', 
					'ReblogParentPortraitURL-128', 'ReblogRootPortraitURL-16', 'ReblogRootPortraitURL-24', 
					'ReblogRootPortraitURL-30', 'ReblogRootPortraitURL-40', 'ReblogRootPortraitURL-48', 
					'ReblogRootPortraitURL-64', 'ReblogRootPortraitURL-96', 'ReblogRootPortraitURL-128', 
					'PostAuthorPortraitURL-16', 'PostAuthorPortraitURL-24', 'PostAuthorPortraitURL-30', 
					'PostAuthorPortraitURL-40', 'PostAuthorPortraitURL-48', 'PostAuthorPortraitURL-64', 
					'PostAuthorPortraitURL-96', 'PostAuthorPortraitURL-128', 'GroupMemberPortraitURL-16', 
					'GroupMemberPortraitURL-24', 'GroupMemberPortraitURL-30', 'GroupMemberPortraitURL-40', 
					'GroupMemberPortraitURL-48', 'GroupMemberPortraitURL-64', 'GroupMemberPortraitURL-96', 
					'GroupMemberPortraitURL-128', 'PhotoURL-500', 'PhotoURL-250', 'PhotoURL-100', 'PhotoURL-75sq', 'Video-500', 
					'Video-250', 'FollowedPortraitURL-16', 'FollowedPortraitURL-24', 'FollowedPortraitURL-30', 
					'FollowedPortraitURL-40', 'FollowedPortraitURL-48', 'FollowedPortraitURL-64', 'FollowedPortraitURL-96', 
					'FollowedPortraitURL-128');
		
		$block = array ('block:Posts', 'block:Description', 'block:Text', 'block:Title', 'block:Photo', 'block:Caption', 
						'block:Quote', 'block:Source', 'block:Link', 'block:Chat', 'block:Lines', 'block:Label', 
						'block:Video', 'block:PreviousPage', 'block:NextPage', 'block:PermalinkPage', 'block:IndexPage', 
						'block:PostTitle', 'block:Pagination', 'block:PermalinkPagination', 'block:PreviousPost', 
						'block:NextPost', 'block:Audio', 'block:Post5', 'block:RebloggedFrom', 'block:GroupMembers', 
						'block:GroupMember', 'block:NewDayDate', 'block:SameDayDate', 'block:DayPage', 'block:DayPagination', 
						'block:PreviousDayPage', 'block:NextDayPage', 'block:Following', 'block:Followed', 'color:Text', 
						'color:Background',
						'block:Post1', 'block:Post2', 'block:Post3', 'block:Post4', 'block:Post5', 'block:Post6', 
						'block:Post7', 'block:Post8', 'block:Post9', 'block:Post10', 'block:Post11', 'block:Post12', 
						'block:Post13', 'block:Post14', 'block:Post15');
						
		return array ( 'single' => $single, 'block' => $block );
	}

}

?>