<?php

#include_once('Ecom/Core/Block/Template.php');
#include_once('Ecom/Core/Block/Form/Element.php');
/**
 * Form widget
 *
 * @package    Ecom
 * @subpackage Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Ecom_Core_Block_Form extends Ecom_Core_Block_Template 
{
    /**
     * Form elements
     * 
     * array(
     *      [$elementId] => $elementType
     * )
     *
     * @var array
     */
    protected $_elements = array();
    
    /**
     * Form fields
     *
     * @var array([$elementId]=>$elementType)
     */
    protected $_fields = array();
    
    /**
     * Foerm buttons
     *
     * @var array([$elementId]=>$elementType)
     */
    protected $_buttons = array();
    
    /**
     * Elements group
     * 
     * array(
     *      [$groupId] => array(
     *          ['label']    => string
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
	public public function __construct($attributes = array()) 
	{
		parent::__construct($attributes);
		
		if (is_null($this->getAttribute('isFormFramed'))) {
			$this->setAttribute('isFormFramed', true);
		}
		if (is_null($this->getAttribute('method'))) {
			$this->setAttribute('method', 'post');
		}
		if (is_null($this->getAttribute('enctype'))) {
			$this->setAttribute('enctype', 'multipart/form-data');
		}
	}
	
	/**
	 * Add form field
	 *
	 * @param  string $elementId
	 * @param  string $elementType
	 * @param  array  $elementConfig
	 * @param  string || bool $after
	 * @return Ecom_Core_Block_Form
	 */
	public function addField($elementId, $elementType, $elementConfig, $after=true)
	{
	    $this->_addElement($elementId, $elementType, $elementConfig, $after);
	    $this->_fields[$elementId] = $elementType;
	    return $this;
	}
	
	/**
	 * Add form button
	 *
	 * @param  string $elementId
	 * @param  string $elementType
	 * @param  array  $elementConfig
	 * @param  string || bool $after
	 * @return Ecom_Core_Block_Form
	 */
	public function addButton($elementId, $elementType, $elementConfig, $after=true)
	{
	    $this->_addElement($elementId, $elementType, $elementConfig, $after);
	    $this->_buttons[$elementId] = $elementType;
	    return $this;
	}
	

	/**
	 * Add form element
	 *
	 * @param  string $elementId
	 * @param  string $elementType
	 * @param  array  $elementConfig
	 * @param  string || bool $after
	 * @return Ecom_Core_Block_Form
	 */
	protected function _addElement($elementId, $elementType, $elementConfig, $after=true)
	{
	    $this->setChild(
	       $elementId,
	       Ecom_Core_Block_Form_Element::factory($elementType, $elementConfig)
	    );
		$this->_elements[$elementId] = $elementType;
		return $this;
	}
	
	/**
	 * Add elements group
	 * 
	 * TODO: after flag
	 * 
	 * @param  string $groupId
	 * @param  array  $groupElements
	 * @param  string $label
	 * @param  string || bool $after
	 * @return Ecom_Core_Block_Form
	 */
    public function addGroup($groupId, $groupElements, $label, $after=true)
    {
        $groupInfo = array();
        $groupInfo['label']     = $label;
        $groupInfo['elements']  = $groupElements;
        
        $this->_elementGroups[$groupId] = $groupInfo;
        
        return $this;
	}
	
    public function getElement($elementId)
    {
    	return isset($this->_elements[$elementId]) ? $this->_children[$elementId] : null;
    }
    
    function getGroup($groupId='')
    {
        if (empty($groupId)) {
        	return $this->_elementGroups;
        }
        elseif (isset($this->_elementGroups[$groupId])){
            return $this->_elementGroups[$groupId];
        }
        return false;
    }
    
    function getFields()
    {
        return array_keys($this->_fields);
    }
    
    function getButtons()
    {
        return array_keys($this->_buttons);
    }
    
    public function getElementsIdByType($type)
    {
        $arrElements = array();
    	if (is_array($type)) {
    		foreach ($type as $typeName) {
    		    $arrElements = array_merge($arrElements, array_keys($this->_elements, $typeName));
    		}
    	}
    	else {
    		$arrElements = array_keys($this->_elements, $type);
    	}
    	return $arrElements;
    }
    
    public function setElementAttribute($attribName, $attribValue)
    {
    	return $this;
    }
    
    public function setElementsValues($arrValues)
    {
        return $this;
    }
    
    public function deleteField($elementId)
    {
        if (isset($this->_fields[$elementId])) {
        	unset($this->_fields[$elementId]);
        	
        	$this->_deleteElement($elementId);
        }
        return $this;
    }
    
    public function deleteButton($elementId)
    {
        if (isset($this->_buttons[$elementId])) {
        	unset($this->_buttons[$elementId]);
        	
        	$this->_deleteElement($elementId);
        }
        return $this;
    }
    
    protected function _deleteElement($elementId)
    {
        if (isset($this->_elements[$elementId])) {
        	unset($this->_elements[$elementId]);
        	unset($this->_children[$elementId]);
        }
        return $this;
    }
    
    function deleteGroup($groupId)
    {
        if (isset($this->_elementGroups[$groupId])) {
        	unset($this->_elementGroups[$groupId]);
        }
        return $this;
    }
    
    public function renderView()
    {
        $formAttributes = array('name', 'id', 'method', 'enctype', 'action', 'target', 'onsubmit', 'class', 'style');
    	$this->getView()->assign('formAttributes', $this->_attributesToString($formAttributes));
    	
    	return parent::renderView();
    }
}