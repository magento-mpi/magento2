<?php

/**
 * Abstract form element
 *
 * @package    Mage
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
abstract class Mage_Core_Block_Form_Element_Abstract extends Mage_Core_Block_Abstract 
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