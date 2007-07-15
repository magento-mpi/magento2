<?php
/**
 * Form element collection
 *
 * @package    Varien
 * @subpackage Data
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Varien_Data_Form_Element_Collection implements ArrayAccess, IteratorAggregate
{
    private $_elements;
    private $_container;
    
    public function __construct($container) 
    {
        $this->_elements = array();
        $this->_container = $container;
    }
    
    /**
    * Implementation of IteratorAggregate::getIterator()
    */
    public function getIterator()
    {
        return new ArrayIterator($this->_elements);
    }

    /**
    * Implementation of ArrayAccess:offsetSet()
    */
    public function offsetSet($key, $value)
    {
        $this->_elements[$key] = $value;
    }
    
    /**
    * Implementation of ArrayAccess:offsetGet()
    */
    public function offsetGet($key)
    {
        return $this->_elements[$key];
    }
    
    /**
    * Implementation of ArrayAccess:offsetUnset()
    */
    public function offsetUnset($key)
    {
        unset($this->_elements[$key]);
    }
    
    /**
    * Implementation of ArrayAccess:offsetExists()
    */
    public function offsetExists($key)
    {
        return isset($this->_elements[$key]);
    }
    
    /**
    * Add element to collection
    * 
    * @todo get it straight with $after
    * @param $element Varien_Data_Form_Element_Abstract
    * @param $after boolean|null|string
    * @return Varien_Data_Form_Element_Abstract
    */
    public function add(Varien_Data_Form_Element_Abstract $element, $after=false)
    {
        // Set the Form for the node
        if ($this->_container->getForm() instanceof Varien_Data_Form) {
            $element->setContainer($this->_container);
            $element->setForm($this->_container->getForm());
        }
        
        if ($after === false) {
            $this->_elements[] = $element;
        }
        elseif ($after === null) {
        	array_unshift($this->_elements, $element);
        }
        elseif (is_string($after)) {
            $newOrderElements = array();
        	foreach ($this->_elements as $index => $currElement) {
        	    if ($currElement->getId() == $after) {
        	        $newOrderElements[] = $currElement;
        	        $newOrderElements[] = $element;
        	        $this->_elements = array_merge($newOrderElements, array_slice($this->_elements, $index+1));
        	        return $element;
        	    }
        	    $newOrderElements[] = $currElement;
        	}
        	$this->_elements[] = $element;
        }

        return $element;
    }
    
    public function remove($elementId)
    {
        foreach ($this->_elements as $index => $element) {
        	if ($elementId == $element->getId()) {
        	    unset($this->_elements[$index]);
        	}
        }
    }
    
    public function count()
    {
        return count($this->_elements);
    }

    public function searchById($elementId)
    {
        foreach ($this->_elements as $element) {
            if ($node->getId() == $elementId) {
                return $element;
            }
        }
        return null;
    }
}
