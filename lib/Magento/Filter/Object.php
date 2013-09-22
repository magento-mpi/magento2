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

class Object extends \Zend_Filter
{
    protected $_columnFilters = array();

    /**
     * @var Magento_ObjectManager
     */
    protected $_entityFactory;

    /**
     * @param Magento_Core_Model_EntityFactory $entityFactory
     */
    public function __construct(Magento_Core_Model_EntityFactory $entityFactory)
    {
        $this->_entityFactory = $entityFactory;
    }
    
    function addFilter(\Zend_Filter_Interface $filter, $column='')
    {
        if (''===$column) {
            parent::addFilter($filter);
        } else {
            if (!isset($this->_columnFilters[$column])) {
                $this->_columnFilters[$column] = new \Zend_Filter();
            }
            $this->_columnFilters[$column]->addFilter($filter);
        }
    }
    
    function filter($object)
    {
        if (!$object instanceof \Magento\Object) {
            throw new \Exception('Expecting an instance of \Magento\Object');
        }
        $class = get_class($object);
        $out = $this->_entityFactory->create($class);
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
