<?php

class Varien_Filter_Object extends Zend_Filter
{
    protected $_columnFilters = array();
    
    function addFilter(Zend_Filter_Interface $filter, $column='')
    {
        if (''===$column) {
            parent::addFilter($filter);
        } else {
            if (!isset($this->_columnFilters[$column])) {
                $this->_columnFilters[$column] = new Zend_Filter();
            }
            $this->_columnFilters[$column]->addFilter($filter);
        }
    }
    
    function filter($object)
    {
        if (!$object instanceof Varien_Object) {
            throw new Exception('Expecting an instance of Varien_Object');
        }
        $class = get_class($object);
        $out = new $class;
        foreach ($object->getData() as $column=>$value) {
            $value = parent::filter($value);
            if (isset($this->_columnFilters[$column])) {
                $value = $this->_columnFilters[$column]->filter($value);
            }
            $out->setData($column, $value);
        }
        return $out;
    }
}