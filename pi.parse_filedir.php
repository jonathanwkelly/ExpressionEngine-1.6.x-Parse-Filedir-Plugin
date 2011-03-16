<?php

/*
=====================================================
 ExpressionEngine - by EllisLab
-----------------------------------------------------
 http://expressionengine.com/
-----------------------------------------------------
 Copyright (c) 2003 EllisLab, Inc.
=====================================================
 THIS IS COPYRIGHTED SOFTWARE
 PLEASE READ THE LICENSE AGREEMENT
 http://expressionengine.com/docs/license.html
=====================================================
 File: pi.parse_filedir.php
-----------------------------------------------------
 Purpose: Some parsing inside of EE does not parse
 the {filedir_#} values...until now.
=====================================================
*/

$plugin_info = array(
	'pi_name'			=> 'Filedir Parser',
	'pi_version'		=> '1.0',
	'pi_author'			=> 'Jonathan Kelly',
	'pi_author_url'		=> 'http://twitter.com/jonathanwkelly',
	'pi_description'	=> 'Parses and returns the {filedir_#} value, where it would previously not be parsed.',
	'pi_usage'			=> Parse_filedir::usage()
);


class Parse_filedir {

    var $return_data;

    /** ----------------------------------------
    /**  Parser
    /** ----------------------------------------*/

    function Parse_filedir() {

        global $TMPL, $DB;

		// Default response
		$this->return_data = $TMPL->tagdata;
		
		// First, try to get the filedir ID
		preg_match_all('/\{filedir\_([0-9]+)\}/', $TMPL->tagdata, $matches);
		
		// If we have a match, try to find the file upload prefs in the DB
		if(count($matches) && isset($matches[1][0])) {
			// Get all the paths from the DB, based on what was matched
			$q = "SELECT `id`, `url` FROM `".$DB->prefix."upload_prefs` WHERE `id` IN ('".implode("','", $matches[1])."') LIMIT 1";
			$query = $DB->query($q);
			if(count($query->result)) {
				foreach($query->result as $result) {
					$this->return_data = str_replace('{filedir_'.$result['id'].'}', $result['url'], $TMPL->tagdata);
				}
			}
		}
		
		return;

    }
    /* END */
    
	// ----------------------------------------
	//  Plugin Usage
	// ----------------------------------------

	// This function describes how the plugin is used.
	//  Make sure and use output buffering

	function usage() {

		ob_start(); 
		?>
Will take a file upload directory and return its path. Useful for things like category descriptions where these are not currently parsed.

{exp:parse_filedir}

{filedir_#}

{/exp:parse_filedir}

Will return /path/to/filedir/

		<?php
		$buffer = ob_get_contents();
		ob_end_clean(); 
		return $buffer;
	}
	/* END */

}
// END CLASS
?>
