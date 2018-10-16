<?php
/**
 * Class to adapt field before display
 * return ONLY htmlParams property
 * @see field
 */
class fieldAdapterPts {
    const DB = 'dbPts';
    const HTML = 'htmlPts';
    const STR = 'str';
    static public $userfieldDest = array('registration', 'shipping', 'billing');
    static public $countries = array();
    static public $states = array();
    /**
     * Executes field Adaption process
     * @param object type field or value $fieldOrValue if DB adaption - this must be a value of field, elase if html - field object
     */
    static public function _($fieldOrValue, $method, $type) {
        if(method_exists('fieldAdapterPts', $method)) {
            switch($type) {
                case self::DB:
                    return self::$method($fieldOrValue);
                    break;
                case self::HTML:
                    self::$method($fieldOrValue);
                    break;
                case self::STR:
                    return self::$method($fieldOrValue);
                    break;
            }
        }
        return $fieldOrValue;
    }
    static public function userFieldDestHtml($field) {
        $field->htmlParams['optionsPts'] = array();
        if(!is_array($field->value)) {
            if(empty($field->value)) 
                $field->value = array();
            else
                $field->value = json_decode($field->value);
        }
        foreach(self::$userfieldDest as $d) {
            $field->htmlParams['optionsPts'][] = array(
                'id' => $d,
                'text' => $d,
                'checked' => in_array($d, $field->value)
            );
        }
    }
    static public function userFieldDestToDB($value) {
        return utilsPts::jsonEncode($value);
    }
    static public function userFieldDestFromDB($value) {
        return utilsPts::jsonDecode($value);
    }
    static public function taxDataHtml($field) {
        $listOfDest = array();
        if(!is_array($field->value)) {
            if(empty($field->value)) 
                $field->value = array();
            else
                $field->value = (array)json_decode($field->value, true);
        }
        foreach(self::$userfieldDest as $d) {
            $listOfDest[] = array(
                'id' => $d,
                'text' => $d,
                'checked' => (is_array($field->value['dest']) && in_array($d, $field->value['dest']))
            );
        }
        $categories = framePts::_()->getModule('products')->getCategories();
        $brands = framePts::_()->getModule('products')->getBrands();
        $cOptions = array();
        $bOptions = array();
        if(!empty($categories)) {
            if(!is_array($field->value['categories']))
                    $field->value['categories'] = array();
            foreach($categories as $c) {
                $cOptions[] = array('id' => $c->term_taxonomy_id, 
                    'text' => $c->cat_name,
                    'checked' => in_array($c->term_taxonomy_id, $field->value['categories']));
            }
        }
        if(!empty($brands)) {
            if(!is_array($field->value['brands']))
                    $field->value['brands'] = array();
            foreach($brands as $b) {
                $bOptions[] = array('id' => $b->term_taxonomy_id, 
                    'text' => $b->cat_name,
                    'checked' => in_array($b->term_taxonomy_id, $field->value['brands']));
            }
        }
        return '<div>'. __('Apply To', PTS_LANG_CODE). '
            <div id="tax_address">
                <b>'. __('Address', PTS_LANG_CODE). '</b><br />
                '. __('Destination', PTS_LANG_CODE). ':'. htmlPts::checkboxlist('params[dest]', array('optionsPts' => $listOfDest)). '<br />
                '. __('Country', PTS_LANG_CODE). ':'. htmlPts::countryList('params[country]', array('notSelected' => true, 'value' => $field->value['country'])). '<br />
            </div>
            <div id="tax_category">
                <b>'. __('Categories', PTS_LANG_CODE). '</b><br />
                '. (empty($cOptions) ? __('You have no categories', PTS_LANG_CODE) : htmlPts::checkboxlist('params[categories][]', array('optionsPts' => $cOptions))). '<br />
                    <b>'. __('Brands', PTS_LANG_CODE). '</b><br />
                '. (empty($bOptions) ? __('You have no brands', PTS_LANG_CODE) : htmlPts::checkboxlist('params[brands][]', array('optionsPts' => $bOptions))). '<br />
            </div>
            <div>'. __('Tax Rate', PTS_LANG_CODE).': '. htmlPts::text('params[rate]', array('value' => $field->value['rate'])).'</div>
            <div>'. __('Absolute', PTS_LANG_CODE).': '. htmlPts::checkbox('params[absolute]', array('checked' => $field->value['absolute'])).'</div>
        </div>';
    }
    static public function displayCountry($cid, $key = 'name') {
        if($key == 'name') {
            $countries = self::getCountries();
            return $countries[$cid];
        } else {
            if(empty(self::$countries))
                self::$countries = self::getCachedCountries();
            foreach(self::$countries as $c) {
                if($c['id'] == $cid)
                    return $c[ $key ];
            }
        }
        return false;
    }
    static public function displayState($sid, $key = 'name') {
        $states = self::getStates();
        return empty($states[$sid]) ? $sid : $states[$sid][$key];
    }
    static public function getCountries($notSelected = false) {
        static $options = array();
        if(empty($options[ $notSelected ])) {
			$options[ $notSelected ] = array();
            if(empty(self::$countries))
                self::$countries = self::getCachedCountries();
            if($notSelected) {
				$options[ $notSelected ][0] = is_bool($notSelected) ? __('Not selected', PTS_LANG_CODE) : __($notSelected);
			}
            foreach(self::$countries as $c) $options[ $notSelected ][$c['id']] = $c['name'];
        }
        return $options[ $notSelected ];
    }
    static public function getStates($notSelected = false) {
        static $options = array();
        if(empty($options[ $notSelected ])) {
			$options[ $notSelected ] = array();
            if(empty(self::$states))
                self::$states = self::getCachedStates();
            if($notSelected) {
				$notSelectedLabel = is_bool($notSelected) ? 'Not selected' : $notSelected;
				$options[ $notSelected ][0] = array('name' => __( $notSelectedLabel ), 'country_id' => NULL);
			}
            foreach(self::$states as $s) $options[ $notSelected ][$s['id']] = $s;
        }
        return $options[ $notSelected ];
    }
    /**
     * Function to get extra field options 
     * 
     * @param object $field
     * @return string 
     */
    static public function getExtraFieldOptions($field_id) {
        $output = '';
        if ($field_id == 0) return '';
        $options = framePts::_()->getModule('optionsPts')->getHelper()->getOptions($field_id);
        if (!empty($options)) {
            foreach ($options as $key=>$value) {
                $output .= '<p>'.$value.'<span class="delete_option" rel="'.$key.'"></span></p>';
            }
        }
        return $output;
    }
    /**
     * Function to get field params
     * 
     * @param object $params 
     */
    static public function getFieldAttributes($params){
        $output = '';
        if (!empty($params->attr)) {
            foreach ($params->attr as $key=>$value) {
                $output .= __($key).':<br />';
                $output .= htmlPts::text('params[attr]['.$key.']',array('value'=>$value)).'<br />';
            }
        } else {
                $output .= __('class', PTS_LANG_CODE).':<br />';
                $output .= htmlPts::text('params[attr][class]',array('value'=>'')).'<br />';
                $output .= __('id', PTS_LANG_CODE).':<br />';
                $output .= htmlPts::text('params[attr][id]',array('value'=>'')).'<br />';
        }
        return $output;
    }
    /**
     * Generating the list of categories for product extra fields
     * 
     * @param object $field 
     */
    static function productFieldCategories($field){
        if(!empty($field->htmlParams['optionsPts']))
            return;
        /*$field->htmlParams['attrs'] = 'id="select_product_field_cat" rel="0"';
        $field->htmlParams['optionsPts'] = array();
        $categories = framePts::_()->getModule('products')->getCategories();
        if(!empty($categories)) {
            if(!is_array($field->value['categories']))
                    $field->value['categories'] = array();
            $field->htmlParams['optionsPts'][0] = in_array(0,$field->value['categories'])?__('Deselect All'):__('Select All', PTS_LANG_CODE);
            foreach($categories as $c) {
                $field->htmlParams['optionsPts'][$c->term_taxonomy_id] = $c->cat_name;
            }
        }*/
    }
    static public function intToDB($val) {
        return intval($val);
    }
    static public function floatToDB($val) {
        return floatval($val);
    }
	/**
	 * Save this in static var - to futher usage
	 * @return array with countries
	 */
	static public function getCachedCountries($clearCache = false) {
		if(empty(self::$countries) || $clearCache)
			self::$countries = framePts::_()->getTable('countries')->getAll('id, name, iso_code_2, iso_code_3');
		return self::$countries;
	}
	/**
	 * Save this in static var - to futher usage
	 * @return array with states
	 */
	static public function getCachedStates($clearCache = false) {
		if(empty(self::$states) || $clearCache)
			self::$states = framePts::_()->getTable('states')
				->leftJoin( framePts::_()->getTable('countries'), 'country_id' )
				->getAll('toe_states.id,
					toe_states.name, 
					toe_states.code, 
					toe_states.country_id, 
					toe_cry.name AS c_name,
					toe_cry.iso_code_2 AS c_iso_code_2, 
					toe_cry.iso_code_3 AS c_iso_code_3');
		return self::$states;
	}
}