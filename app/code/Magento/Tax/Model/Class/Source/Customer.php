<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Tax_Model_Class_Source_Customer extends Magento_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * @var Magento_Tax_Model_Resource_Class_CollectionFactory
     */
    protected $_classesFactory;

    /**
     * @param Magento_Tax_Model_Resource_Class_CollectionFactory $classesFactory
     */
    public function __construct(Magento_Tax_Model_Resource_Class_CollectionFactory $classesFactory)
    {
        $this->_classesFactory = $classesFactory;
    }

    public function getAllOptions()
    {
        if (!$this->_options) {
            /** @var $classCollection Magento_Tax_Model_Resource_Class_Collection */
            $classCollection = $this->_classesFactory->create();
            $this->_options = $classCollection->addFieldToFilter(
                'class_type', Magento_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER
            )->load()->toOptionArray();
        }
        return $this->_options;
    }
}
