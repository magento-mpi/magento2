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
    public function add(Varien_Data_Form_Element_Abstract $element)
    {
        //$element->setParent($this->_container);

        // Set the Tree for the node
        if ($this->_container->getForm() instanceof Varien_Data_Form) {
            $element->setForm($this->_container->getForm());
        }

        $this->_elemetnts[] = $element;

        return $element;
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
