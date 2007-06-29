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
    private $_elemetnts;
    private $_container;
    
    public function __construct($container) 
    {
        $this->_elemetnts = array();
        $this->_container = $container;
    }
    
    /**
    * Implementation of IteratorAggregate::getIterator()
    */
    public function getIterator()
    {
        return new ArrayIterator($this->_elemetnts);
    }

    /**
    * Implementation of ArrayAccess:offsetSet()
    */
    public function offsetSet($key, $value)
    {
        $this->_elemetnts[$key] = $value;
    }
    
    /**
    * Implementation of ArrayAccess:offsetGet()
    */
    public function offsetGet($key)
    {
        return $this->_elemetnts[$key];
    }
    
    /**
    * Implementation of ArrayAccess:offsetUnset()
    */
    public function offsetUnset($key)
    {
        unset($this->_elemetnts[$key]);
    }
    
    /**
    * Implementation of ArrayAccess:offsetExists()
    */
    public function offsetExists($key)
    {
        return isset($this->_elemetnts[$key]);
    }
    
    /**
    * Add element to collection
    */
    public function add(Varien_Data_Form_Element_Abstract $element, $after=false)
    {
        // Set the Form for the node
        if ($this->_container->getForm() instanceof Varien_Data_Form) {
            $element->setForm($this->_container->getForm());
        }
        
        if ($after === false) {
            $this->_elemetnts[] = $element;
        }
        elseif ($after === null) {
        	array_unshift($this->_elemetnts, $element);
        }
        elseif (is_string($after)) {
            $newOrderElements = array();
        	foreach ($this->_elemetnts as $index => $currElement) {
        	    if ($currElement->getId() == $after) {
        	        $newOrderElements[] = $currElement;
        	        $newOrderElements[] = $element;
        	        $this->_elemetnts = array_merge($newOrderElements, array_slice($this->_elemetnts, $index+1));
        	        return $element;
        	    }
        	    $newOrderElements[] = $currElement;
        	}
        	$this->_elemetnts[] = $element;
        }
        
        

        return $element;
    }
    
    public function remove($elementId)
    {
        foreach ($this->_elemetnts as $index => $element) {
        	if ($elementId == $element->getId()) {
        	    unset($this->_elemetnts[$index]);
        	}
        }
    }
    
    public function count()
    {
        return count($this->_elemetnts);
    }

    public function searchById($elementId)
    {
        foreach ($this->_elemetnts as $element) {
            if ($node->getId() == $elementId) {
                return $element;
            }
        }
        return null;
    }
}
