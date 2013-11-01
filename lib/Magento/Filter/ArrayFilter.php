<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Filter
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Filter;

class ArrayFilter extends \Zend_Filter
{
    /**
     * @var array
     */
    protected $_columnFilters = array();

    /**
     * {@inheritdoc}
     *
     * @param \Zend_Filter_Interface $filter
     * @param string $column
     * @return null|\Zend_Filter
     */
    public function addFilter(\Zend_Filter_Interface $filter, $column = '')
    {
        if ('' === $column) {
            parent::addFilter($filter);
        } else {
            if (!isset($this->_columnFilters[$column])) {
                $this->_columnFilters[$column] = new \Zend_Filter();
            }
            $this->_columnFilters[$column]->addFilter($filter);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param array $array
     * @return array
     */
    public function filter($array)
    {
        $out = array();
        foreach ($array as $column => $value) {
            $value = parent::filter($value);
            if (isset($this->_columnFilters[$column])) {
                $value = $this->_columnFilters[$column]->filter($value);
            }
            $out[$column] = $value;
        }
        return $out;
    }
}
