<?php
/**
 * Data form
 *
 * @package    Varien
 * @subpackage Data
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Varien_Data_Form extends Varien_Data_Form_Abstract
{
    /**
     * All form elements collection
     *
     * @var Varien_Data_Form_Element_Collection
     */
    protected $_allElements;

    /**
     * form elements index
     *
     * @var array
     */
    protected $_elementsIndex;

    public function __construct($attributes = array()) 
    {
        parent::__construct($attributes);
        $this->_allElements = new Varien_Data_Form_Element_Collection($this);
    }
    
    /**
     * Add form element
     *
     * @param   Varien_Data_Form_Element_Abstract $element
     * @return  Varien_Data_Form
     */
    public function addElement(Varien_Data_Form_Element_Abstract $element, $after=false)
    {
        $this->checkElementId($element->getId());
        parent::addElement($element);
        $this->addElementToCollection($element);
        return $this;
    }
    
    protected function _elementIdExists($elementId)
    {
        return isset($this->_elementsIndex[$elementId]);
    }
    
    public function addElementToCollection($element)
    {
        $this->_elementsIndex[$element->getId()] = $element;        
        $this->_allElements->add($element);
        return $this;
    }
    
    public function checkElementId($elementId)
    {
        if ($this->_elementIdExists($elementId)) {
            throw new Exception('Element with id "'.$elementId.'" already exists');
        }
        return true;
    }
    
    public function getForm()
    {
        return $this;
    }
    
    public function getElement($elementId)
    {
        if ($this->_elementIdExists($elementId)) {
            return $this->_elementsIndex[$elementId];
        }
        return null;
    }
    
    public function setValues($values)
    {
        foreach ($values as $elementId=>$value) {
            if ($element = $this->getElement($elementId)) {
                $element->setValue($value);
            }
        }
        return $this;
    }
    
    public function addFieldNamePrefix($prefix)
    {
        foreach ($this->_allElements as $element) {
        	$name = $element->getName();
        	if ($name) {
        	    $vars = explode('[', $name);
        	    $newName = $prefix;
        	    foreach ($vars as $index=>$value) {
        	    	$newName.= '['.$value;
        	    	if ($index==0) {
        	    	    $newName.= ']';
        	    	}
        	    }
        	    $element->setName($newName);
        	}
        }
    }

    public function removeField($elementId)
    {
        if ($this->_elementIdExists($elementId)) {
            unset($this->_elementsIndex[$elementId]);
        }
        return $this;
    }
}