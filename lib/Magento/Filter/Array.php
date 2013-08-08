<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Filter
 * @copyright  {copyright}
 * @license    {license_link}
 */


class Magento_Filter_Array extends Zend_Filter
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
        $out = array();
        foreach ($array as $column=>$value) {
            $value = parent::filter($value);
            if (isset($this->_columnFilters[$column])) {
                $value = $this->_columnFilters[$column]->filter($value);
            }
            $out[$column] = $value;
        }
        return $out;
    }
}
