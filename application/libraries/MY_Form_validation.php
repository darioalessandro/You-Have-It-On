<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Form_validation extends Form_validation 
{
	
	function MY_Form_validation($rules = array())
	{	
		$this->CI =& get_instance();
		
		// Validation rules can be stored in a config file.
		$this->_config_rules = $rules;
		
		// Automatically load the form helper
		$this->CI->load->helper('form');

		// Set the character encoding in MB.
		if (function_exists('mb_internal_encoding')) {
			mb_internal_encoding($this->CI->config->item('charset'));
		}
	
		//log_message('debug', "Form Validation Class Initialized");
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Run the Validator
	 *
	 * This function does all the work.
	 *
	 * @access	public
	 * @return	bool
	 */		
	function run($group = '')
	{
		// Do we even have any data to process?  Mm?
		if (count($this->_array_validate) == 0) {
			return FALSE;
		}
		
		// Does the _field_data array containing the validation rules exist?
		// If not, we look to see if they were assigned via a config file
		if (count($this->_field_data) == 0) {
			// No validation rules?  We're done...
			if (count($this->_config_rules) == 0) {
				return FALSE;
			}
			
			// Is there a validation rule for the particular URI being accessed?
			$uri = ($group == '') ? trim($this->CI->uri->ruri_string(), '/') : $group;
			
			if ($uri != '' AND isset($this->_config_rules[$uri])) {
				$this->set_rules($this->_config_rules[$uri]);
			}
			else {
				$this->set_rules($this->_config_rules);
			}
	
			// We're we able to set the rules correctly?
			if (count($this->_field_data) == 0) {
				log_message('debug', "Unable to find validation rules");
				return FALSE;
			}
		}
	
		// Load the language file containing error messages
		//$this->CI->lang->load('form_validation');
							
		// Cycle through the rules for each field, match the 
		// corresponding $this->_array_validate item and test for errors
		foreach ($this->_field_data as $field => $row) {		
			// Fetch the data from the corresponding $this->_array_validate array and cache it in the _field_data array.
			// Depending on whether the field name is an array or a string will determine where we get it from.
			
			if ($row['is_array'] == TRUE) {
				$this->_field_data[$field]['postdata'] = $this->_reduce_array($this->_array_validate, $row['keys']);
			}
			else {
				if (isset($this->_array_validate[$field]) AND $this->_array_validate[$field] != "") {
					$this->_field_data[$field]['postdata'] = $this->_array_validate[$field];
				}
			}
			
			$rules = explode('|', $row['rules']);
			$rule = array ();
			$count = -1;
			$in_expression = FALSE;
			
			foreach ($rules as $rule_key => $rule_val) {
				
				if ($in_expression === TRUE) {
					if (preg_match('/^(.*)\$\/$/', $rule_val)) {
						$in_expression = FALSE;
					}
					$rule[$count] .= '|' . $rule_val;
				}
				else {
					if (strtolower(substr($rule_val, 0, 11)) === 'expression:') {
						$in_expression = TRUE;
					}
					++$count;
					$rule[$count] = $rule_val;
				}
			}
			
			$this->_execute($row, $rule, $this->_field_data[$field]['postdata']);		
		}

		// Did we end up with any errors?
		$total_errors = count($this->_error_array);

		if ($total_errors > 0) {
			$this->_safe_form_data = TRUE;
		}

		// Now we need to re-set the POST data with the new, processed data
		$this->_reset_post_array();
		
		// No errors, validation passes!
		if ($total_errors == 0) {
			return TRUE;
		}

		// Validation fails
		return FALSE;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Executes the Validation routines
	 *
	 * @access	private
	 * @param	array
	 * @param	array
	 * @param	mixed
	 * @param	integer
	 * @return	mixed
	 */	
	function _execute($row, $rules, $postdata = NULL, $cycles = 0)
	{
		// If the $this->_array_validate data is an array we will run a recursive call
		if (is_array($postdata)) { 
			foreach ($postdata as $key => $val) {
				$this->_execute($row, $rules, $val, $cycles);
				$cycles++;
			}
			
			return;
		}
		
		// --------------------------------------------------------------------

		// If the field is blank, but NOT required, no further tests are necessary
		$callback = FALSE;
		if ( ! in_array('required', $rules) AND is_null($postdata)) {
			// Before we bail out, does the rule contain a callback?
			if (preg_match("/(callback_\w+)/", implode(' ', $rules), $match)) {
				$callback = TRUE;
				$rules = (array('1' => $match[1]));
			}
			else {
				return;
			}
		}

		// --------------------------------------------------------------------
		
		// Isset Test. Typically this rule will only apply to checkboxes.
		if (is_null($postdata) AND $callback == FALSE) {
			if (in_array('isset', $rules, TRUE) OR in_array('required', $rules)) {
				// Set the message type
				$type = (in_array('required', $rules)) ? 'required' : 'isset';
			
				if ( ! isset($this->_error_messages[$type])) {
					if (FALSE === ($line = $this->CI->lang->line($type))) {
						$line = 'The field was not set';
					}							
				}
				else {
					$line = $this->_error_messages[$type];
				}
				
				// Build the error message
				$message = sprintf($line, $this->_translate_fieldname($row['label']));

				// Save the error message
				$this->_field_data[$row['field']]['error'] = $message;
				
				if ( ! isset($this->_error_array[$row['field']])) {
					$this->_error_array[$row['field']] = $message;
				}
			}
			
			return;
		}

		// --------------------------------------------------------------------

		// Cycle through each rule and run it
		foreach ($rules As $rule) {
			$_in_array = FALSE;
			
			// We set the $postdata variable with the current data in our master array so that
			// each cycle of the loop is dealing with the processed data from the last cycle
			if ($row['is_array'] == TRUE AND is_array($this->_field_data[$row['field']]['postdata'])) {
				// We shouldn't need this safety, but just in case there isn't an array index
				// associated with this cycle we'll bail out
				if ( ! isset($this->_field_data[$row['field']]['postdata'][$cycles])) {
					continue;
				}
			
				$postdata = $this->_field_data[$row['field']]['postdata'][$cycles];
				$_in_array = TRUE;
			}
			else {
				$postdata = $this->_field_data[$row['field']]['postdata'];
			}

			// --------------------------------------------------------------------
	
			// Is the rule a callback?			
			$callback = FALSE;
			if (substr($rule, 0, 9) == 'callback_') {
				$rule = substr($rule, 9);
				$callback = TRUE;
			}
			
			// Strip the parameter (if exists) from the rule
			// Rules can contain a parameter: max_length[5]
			$param = FALSE;
			if (preg_match("/(expression):/", $rule, $match)) {
				$match = explode(':', $rule);
				$rule	= 'expression';
				$param	= $match[1];
			}
			elseif (preg_match("/(.*?)\[(.*?)\]/", $rule, $match)) {
				$rule	= $match[1];
				$param	= $match[2];
			}
			
			// Call the function that corresponds to the rule
			if ($callback === TRUE) {
				if ( ! method_exists($this->CI, $rule)) {
					continue;
				}
				
				// Run the function and grab the result
				$result = $this->CI->$rule($postdata, $param);

				// Re-assign the result to the master data array
				if ($_in_array == TRUE) {
					$this->_field_data[$row['field']]['postdata'][$cycles] = (is_bool($result)) ? $postdata : $result;
				}
				else {
					$this->_field_data[$row['field']]['postdata'] = (is_bool($result)) ? $postdata : $result;
				}
			
				// If the field isn't required and we just processed a callback we'll move on...
				if ( ! in_array('required', $rules, TRUE) AND $result !== FALSE) {
					continue;
				}
			}
			else {
				if ( ! method_exists($this, $rule)) {
					// If our own wrapper function doesn't exist we see if a native PHP function does. 
					// Users can use any native PHP function call that has one param.
					if (function_exists($rule)) {
						$result = $rule($postdata);
											
						if ($_in_array == TRUE) {
							$this->_field_data[$row['field']]['postdata'][$cycles] = (is_bool($result)) ? $postdata : $result;
						}
						else {
							$this->_field_data[$row['field']]['postdata'] = (is_bool($result)) ? $postdata : $result;
						}
					}
					
					continue;
				}

				$result = $this->$rule($postdata, $param);

				if ($_in_array == TRUE) {
					$this->_field_data[$row['field']]['postdata'][$cycles] = (is_bool($result)) ? $postdata : $result;
				}
				else {
					$this->_field_data[$row['field']]['postdata'] = (is_bool($result)) ? $postdata : $result;
				}
			}
							
			// Did the rule test negatively?  If so, grab the error.
			if ($result === FALSE) {
				if ( ! isset($this->_error_messages[$rule])) {
					if (FALSE === ($line = $this->CI->lang->line($rule))) {
						$line = 'Unable to access an error message corresponding to your field name.';
					}						
				}
				else {
					$line = $this->_error_messages[$rule];
				}
				
				// Is the parameter we are inserting into the error message the name
				// of another field?  If so we need to grab its "field label"
				if (isset($this->_field_data[$param]) AND isset($this->_field_data[$param]['label'])) {
					$param = $this->_field_data[$param]['label'];
				}
				
				// Build the error message
				$message = sprintf($line, $this->_translate_fieldname($row['label']), $param);

				// Save the error message
				$this->_field_data[$row['field']]['error'] = $message;
				
				if ( ! isset($this->_error_array[$row['field']])) {
					$this->_error_array[$row['field']] = $message;
				}
				
				return;
			}
		}
	}

	/**
	 * Match by Regular Expression
	 * 
	 * @access	public
	 * @param string
	 * @param value
	 * @return boolean
	 */
	function expression($str, $val)
	{
		return ( !! preg_match($val, $str) ? TRUE : FALSE);
	}
}