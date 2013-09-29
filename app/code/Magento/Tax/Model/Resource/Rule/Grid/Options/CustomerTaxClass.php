<?php
/**
 * Customer Tax Class option array
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model\Resource\Rule\Grid\Options;

class CustomerTaxClass
    implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * @var \Magento\Tax\Model\Resource\TaxClass\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Tax\Model\Resource\TaxClass\CollectionFactory $collectionFactory
     */
    public function __construct(\Magento\Tax\Model\Resource\TaxClass\CollectionFactory $collectionFactory)
    {
        $this->_collectionFactory = $collectionFactory;
    }

    /**
     * Return Customer Tax Class array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_collectionFactory->create()->setClassTypeFilter(\Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_CUSTOMER)
            ->toOptionHash();
    }
}
