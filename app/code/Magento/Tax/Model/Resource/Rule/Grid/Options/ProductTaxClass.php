<?php
/**
 * Product Tax Class option array
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Tax_Model_Resource_Rule_Grid_Options_ProductTaxClass
    implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var Magento_Tax_Model_Resource_TaxClass_CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param Magento_Tax_Model_Resource_TaxClass_CollectionFactory $collectionFactory
     */
    public function __construct(Magento_Tax_Model_Resource_TaxClass_CollectionFactory $collectionFactory)
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
        return $this->_collectionFactory->create()->setClassTypeFilter(Magento_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT)
            ->toOptionHash();
    }
}
