<?php

class Varien_Filter_Grid extends Zend_Filter
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
    
    function filter($array)
    {
        foreach ($array as $i=>$row) {
            foreach ($row as $column=>$value) {
                $value = parent::filter($value);
                if (isset($this->_columnFilters[$column])) {
                    $value = $this->_columnFilters[$column]->filter($value);
                }
                $array[$i][$column] = $value;
            }
        }
        return $array;
    }
}