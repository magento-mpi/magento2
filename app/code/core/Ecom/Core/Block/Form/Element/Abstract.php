<?php

/**
 * Abstract form element
 *
 * @package    Varien
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
abstract class Ecom_Core_Block_Form_Element_Abstract extends Ecom_Core_Block_Abstract 
{
	public function __construct($attributes = array()) 
	{
		parent::__construct($attributes);
	}

	function renderElementLabel()
	{
	    $html = '';
	    if ($label = $this->getAttribute('label')) {
	    	$html = '<label for="' . $this->getAttribute('id') . '">'.$label.'</label>';
	    }
	    return $html;
	}
}