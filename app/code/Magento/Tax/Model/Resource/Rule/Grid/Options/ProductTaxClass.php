<?php
/**
 * Product Tax Class option array
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model\Resource\Rule\Grid\Options;

class ProductTaxClass
    implements \Magento\Core\Model\Option\ArrayInterface
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
        return $this->_collectionFactory->create()->setClassTypeFilter(\Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_PRODUCT)
            ->toOptionHash();
    }
}
