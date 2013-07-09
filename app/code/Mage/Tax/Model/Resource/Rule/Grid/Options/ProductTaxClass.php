<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Tax_Model_Resource_Rule_Grid_Options_ProductTaxClass
    implements Mage_Core_Model_Option_ArrayInterface
{
    /**
     * @var Mage_Tax_Model_Resource_Class_CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param Mage_Tax_Model_Resource_Class_CollectionFactory $collectionFactory
     */
    public function __construct(Mage_Tax_Model_Resource_Class_CollectionFactory $collectionFactory)
    {
        $this->_collectionFactory = $collectionFactory;
    }

    /**
     * Return Product Tax Class array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_collectionFactory->create()->setClassTypeFilter(Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT)
            ->toOptionHash();
    }
}
