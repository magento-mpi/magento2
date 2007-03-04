<?php

/**
 * Form widget
 *
 * @package    Varien
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Varien_Widget_Form extends Varien_Widget
{
    /**
     * Form elements
     * 
     * array(
     *      [$elementId] => $elementObject
     * )
     *
     * @var array
     */
    protected $_elements = array();
    
    /**
     * Elements group
     * 
     * array(
     *      [$groupId] => array(
     *          ['label']
     *          ['elements'] => array($elementId)
     *      )
     * )
     *
     * @var array
     */
    protected $_elementGroups = array();
    
    /**
     * Constructor
     *
     * @param array $attributes form attributes
     */
	public function __construct($containerId, $attributes) 
	{
		parent::__construct($containerId, $attributes);
		
		if (is_null($this->getAttribute('framed'))) {
			$this->setAttribute('framed', true);
		}
	}
	
	function addElement($elementId, $elementType, $elementConfig, $after)
	{
		
	}
	
	function addGroup($groupId, $groupElements, $after)
	{
		
	}
	
    function getElement($elementName)
    {
    	return isset($this->_elements[$elementName]) ? $this->_elements[$elementName] : null;
    }
    
    function getElementsByType($type)
    {
    	
    }
    
    function setElementAttribute($attribName, $attribValue)
    {
    	return $this;
    }
    
    
}