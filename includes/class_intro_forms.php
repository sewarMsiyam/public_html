<?php


class Intro_Form {
    
    private $form_name;
    private $form_method;
	var $error = array();
	var $error_jquery = '';
	var $errors = array();
	var $data; 
	var $controls = array();
	var $js_validation; 
	var $ss_validation; 
	var $fieldset = 0; 
    
    function __construct($error=''){
		$this->error = $error;
	}
	
	public function iform_start($id = 'form_add', $action = '', $method = 'POST', $fieldset='', $attr_ar = array() ) 
	{
        $str = "<div class=\"forms\">\n";
		
		if($fieldset!=''){
			$str .= "<fieldset><legend>$fieldset</legend>\n";
			$this->fieldset = 1;
		}
		$str .= "<form action=\"$action\" class=\"forms\" method=\"$method\"";
        if ( isset($id) ) {
            $str .= " id=\"$id\"";
        }
        $str .= $attr_ar? $this->addAttributes( $attr_ar ) . ">\n": ">\n";
		$str .= "<input type=\"hidden\" name=\"name_form\" id=\"name_form\" value=\"$id\">\n<table id=\"table_".$id."\">\n";
        
		$this->data .= $str;
		$this->form_name = $id;
		$this->form_method = $method;
		//return $str;
    }
    
    private function addAttributes( $attr_ar ) {
        $str = '';
        $min_atts = array('checked', 'disabled', 'readonly', 'multiple');
        foreach( $attr_ar as $key=>$val ) {
            if ( in_array($key, $min_atts) ) {
                if ( !empty($val) ) { 
                    $str .= " $key=\"$key\"";
                }
            } else {
                $str .= " $key=\"$val\"";
            }
        }
        return $str;
    }

    public function iform_input($type, $name, $lable, $value, $attr_ar = array(), $valid_ar = array() ) {
        
		
		if($valid_ar['req'] == 1){
			$this->js_validation .= " valid.addValidation('$name','req','Please input valid data: $lable.'); \n";
			$star = " <font class=\"star\">*</font> ";
			$this->ss_validation[$type][$name] = $valid_ar;
			$this->ss_validation[$type][$name]['lable'] = $lable;
		}
		if(!$value) $value = $_POST[$name];
		$str = "<tr>\n\t<td>".$this->iform_lable($name, $lable)."$star </td>\n\t<td><input type=\"$type\" name=\"$name\" id=\"$name\" value=\"$value\"";
        if ($attr_ar) {
            $str .= $this->addAttributes( $attr_ar );
        }
		
		if($this->error[$name] != '') $this->error[$name] = "<span class=\"error\">".$this->error[$name]."</span>";
        $str .= " /> ".$this->error[$name]." <span class=\"note\">$valid_ar[note]</span></td>\n</tr>\n";
		$this->data .= $str;
	
        //return $str;
    }
	public function iform_hidden($name, $value, $attr_ar = array() ) {
        $str = "<input type=\"hidden\" name=\"$name\" value=\"$value\"";
        if ($attr_ar) {
            $str .= $this->addAttributes( $attr_ar );
        }
        $str .= " />\n";
		$this->data .= $str;
        //return $str;
    }
	public function iform_fake($name, $value, $attr_ar = array() ) {
        $str = "<input type=\"text\" name=\"$name\" value=\"$value\"";
        if ($attr_ar) {
            $str .= $this->addAttributes( $attr_ar );
        }
        $str .= " style=\"display:none;\" />\n";
		$this->data .= $str;
        //return $str;
    }
	public function iform_button($type,$name, $value, $label, $css='' ) {
        if($css !='') $css = "class=\"$css\"";
		
		$str = "
		<tr>
			<td> </td>
			<td><button type=\"$type\" name=\"$name\" value=\"$value\" $css>$label</button></td>
		</tr>\n";
		
		$this->data .= $str;
        //return $str;
    }
	
	
	public function isValidEmail($email){
		return preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $email);
	}

    
    public function iform_textarea($name, $lable, $rows = 4, $cols = 30, $value = '', $attr_ar = array() ) {
        $str = "<tr>\n\t<td>$lable</td>\n\t<td><textarea name=\"$name\" rows=\"$rows\" cols=\"$cols\"";
        if ($attr_ar) {
            $str .= $this->addAttributes( $attr_ar );
        }
        $str .= ">$value</textarea></td>\n</tr>\n";
		$this->data .= $str;
        //return $str;
    }
	
	public function iform_custom($name,$lable,$data,$valid_ar=array() ) {
        if($valid_ar['req'] == 1){
			$this->js_validation .= " valid.addValidation('$name','dontselect=0','Please input valid data in: $lable.'); \n";
			$star = " <font class=\"star\">*</font> ";
		}
		$str = "<tr>\n\t<td>".$this->iform_lable($name, $lable)." $star</td>\n\t<td>$data</td>\n</tr>\n";
		$this->data .= $str;
        //return $str;
    }
    
    public function iform_lable($forID, $text, $attr_ar = array() ) {
        $str = "<label for=\"$forID\"";
        if ($attr_ar) {
            $str .= $this->addAttributes( $attr_ar );
        }
        $str .= ">$text</label>";
        return $str;
    }

    public function iform_select($name, $lable, $option_list, $bVal = true, $selected_value = NULL, $header = NULL, $attr_ar = array() ) {
        $str = "<tr>\n\t<td>$name<td>\n\t<td><select name=\"$name\"";
        if ($attr_ar) {
            $str .= $this->addAttributes( $attr_ar );
        }
        $str .= ">\n";
        if ( isset($header) ) {
            $str .= "  \t<option value=\"\">$header</option>\n";
        }
        foreach ( $option_list as $val => $text ) {
            $str .= $bVal? "  \t<option value=\"$val\"": "  <option";
            if ( isset($selected_value) && ( $selected_value === $val || $selected_value === $text) ) {
                $str .= ' selected';
            }
            $str .= ">$text</option>\n";
        }
        $str .= "\t</select></td>\n</tr>\n";
        $this->data .= $str;
		//return $str;
    }
	
	public function iform_terms($name, $lable, $terms_text, $value = '') {
        $str = "<tr>\n\t<td>$lable</td>\n\t<td>$terms_text</td>\n</tr>\n";
		$this->data .= $str;
        //return $str;
    }
    
    public function iform_end() {
        $this->data .= "\n</table>\n</form>";
		//return "\n</table>\n</form>";
    }
	
	public function iform_render($return=''){
	
		foreach ($this->errors as $li_errors) {
            if($li_errors!='') 
				$error_messages .= $li_errors;
				else continue;
        }
		
		if ($error_messages != '') {
            $contents = '<div class="error_messages"><ul>' . $error_messages . '</ul></div>';
        }
		if ($this->error['msg'] != '') {
            $contents .= '<div class="error_msg">' . $this->error['msg'] . '</div>';
        }
		$contents .= $this->data;
		/*
		if(strlen($this->js_validation) > 10)
		{
			$contents .= "
				<SCRIPT src=\"style/js/gen_validator.js\" type=\"text/javascript\"></SCRIPT>
				<script type=\"text/javascript\">
				 var valid  = new Validator(\"".$this->form_name."\"); \n";
			$contents .= $this->js_validation;	 
			$contents .= "</script>\n";	
			
		}*/
		//var_dump($this->error_jquery);
		if(strlen($this->error_jquery) > 10)
		{
			$contents .= "
				<script type=\"text/javascript\"> \n";
			$contents .= $this->error_jquery;	 
			$contents .= "</script>\n";	
		}
		if( $this->fieldset == 1 ){
			$contents .= "</fieldset>\n";	
		}
		$contents .= "\n\n</div>\n"; #end the main form div
		
		if($return == 'echo') echo $contents;
		
		else return  $contents;
		
	}
	
	public function validate(){
        global ${'_' . $this->form_method};

        $method = & ${'_' . $this->form_method};

        $form_is_valid = false;

        
        if (isset($method['name_form']) && $method['name_form'] == $this->form_name) {
						
			$form_is_valid = true;
			
			foreach ($this->ss_validation as $type) 
			{
				foreach ($type as $name=>$validation_options) 
				{
					foreach ($validation_options as $key=>$val) 
					{
						if($key == 'req' && $val == 1 && $method[$name] =='')
						{
							$this->errors[] = "<li>من فضلك ادخل: $validation_options[lable] </li>";
							$this->error_jquery .= "$(\"input[name='$name']\").addClass('error_jquery');\n";
							$form_is_valid = false;
						}
						if($key == 'min' && strlen($method[$name]) < $val)
						{
							$this->errors[] = "<li>$validation_options[lable] أقل عدد حروف هو $val</li>";
							$this->error_jquery .= "$(\"input[name='$name']\").addClass('error_jquery');\n";
							$form_is_valid = false;
						}
						if($key == 'match' && $method[$name] != $method[$val])
						{
							$this->errors[] = "<li>$validation_options[lable] : غير متطابقة</li>";
							$this->error_jquery .= "$(\"input[name='$name']\").addClass('error_jquery');\n";
							$form_is_valid = false;
						}
						if($key == 'email' && $val == 1 && $this->isValidEmail($method[$name]) == false)
						{
							$this->errors[] = "<li>$validation_options[lable] : خطأ : من فضلك أدخل بريد الكتروني بشكل صحيح</li>";
							$this->error_jquery .= "$(\"input[name='$name']\").addClass('error_jquery');\n";
							$form_is_valid = false;
						}
						if($key == 'duplicated' && $val == true)
						{
							$this->errors[] = "<li>خطأ: $validation_options[lable] مسجل لدينا مسبقا.</li>";
							$this->error_jquery .= "$(\"input[name='$name']\").addClass('error_jquery');\n";
							$form_is_valid = false;
						}
						if($key == 'check_login' && $val == 'false')
						{
							$this->errors[] = "<li>خطأ في عملية الدخول. يرجى التاكد من البيانات..</li>";
							$this->error_jquery .= "$(\"input[name='$name']\").addClass('error_jquery');\n";
							$form_is_valid = false;
						}
						
						
					}
				}
				//var_dump($type);
			}

		}else{
		
			$form_is_valid = false;
		}
		
		return $form_is_valid;
		
		//var_dump($method);
		
		
	}
	function validate_control($control)
    {
	
	}
    
}
?>