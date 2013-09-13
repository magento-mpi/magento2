<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Filter
 * @copyright  {copyright}
 * @license    {license_link}
 */


class Magento_Filter_Object extends Zend_Filter
{
    protected $_columnFilters = array();

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }
    
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
        if (!$object instanceof Magento_Object) {
            throw new Exception('Expecting an instance of Magento_Object');
        }
        $class = get_class($object);
        $out = $this->_objectManager->create($class);
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
