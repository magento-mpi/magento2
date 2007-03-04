<?php

/**
 * Base widget class
 *
 * @package    Ecom
 * @module     module
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Varien_Widget
{
    /**
     * ID of HTML element where widget is display
     *
     * @var string
     */
    protected $_containerId;
    
    /**
     * Attributes
     *
     * @var array
     */
    protected $_attributes;
	
    /**
     * Constructor
     *
     * @param string $containerId
     * @param array  $attributes
     */
    public function __construct($containerId, $attributes = array())
	{
		$this->_containerId = $containerId;
		$this->_attributes  = $attributes;
	}
	
	/**
	 * Get attribute value
	 *
	 * @param  string $attribName
	 * @return mixed
	 */
    function getAttribute($attribName)
    {
    	return isset($this->_attribs[$attribName]) ? $this->_attribs[$attribName] : null;
    }
    
    /**
     * Get container id
     *
     * @return string
     */
    function getContainerId()
    {
    	return $this->_containerId;
    }
    
    /**
     * Set attribute value
     *
     * @param   string $attribName
     * @param   mixed  $attribValue
     * @return  Varien_Widget
     */
    function setAttribute($attribName, $attribValue)
    {
    	$this->_attribs[$attribName] = $attribValue;
    	return $this;
    }
    
    function getAttributesString($requiredAttributes = array())
    {
        $arrValues = array();
    	if (!empty($requiredAttributes)) {
    		foreach ($requiredAttributes as $attribute) {
    		}
    	}
    	else {
    		foreach ($this->_attributes as $attribute => $value) {
    			$arrValues[] = $attribute . '="' . $this->_escapeAttributeValue($value) . '"';
    		}
    	}
    	
    	return implode(' ', $arrValues);
    }
    
    function _escapeAttributeValue($value)
    {
    	return htmlspecialchars($value, ENT_QUOTES);
    }
}