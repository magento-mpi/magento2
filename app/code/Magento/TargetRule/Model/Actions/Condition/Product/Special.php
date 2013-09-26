<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * TargetRule Action Special Product Attributes Condition Model
 *
 * @category   Magento
 * @package    Magento_TargetRule
 */
namespace Magento\TargetRule\Model\Actions\Condition\Product;

class Special
    extends \Magento\Rule\Model\Condition\Product\AbstractProduct
{
    /**
     * Set condition type and value
     *
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Catalog\Model\Resource\Product $productResource
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Set\CollectionFactory $eavEntitySetFactory
     * @param \Magento\Backend\Helper\Data $backendData
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Eav\Model\Config $config
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Model\Resource\Product $productResource
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Set\Collection $attrSetCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\Resource\Product $productResource,
        \Magento\Eav\Model\Resource\Entity\Attribute\Set\CollectionFactory $eavEntitySetFactory,
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Eav\Model\Config $config,
        \Magento\Catalog\Model\Product $product,
        \Magento\Catalog\Model\Resource\Product $productResource,
        \Magento\Eav\Model\Resource\Entity\Attribute\Set\Collection $attrSetCollection,
        array $data = array()
    ) {
        parent::__construct(
            $eavConfig, $productResource, $eavEntitySetFactory, $backendData, $context, $config, $product, 
            $productResource, $attrSetCollection, $data
        );
        $this->setType('Magento\TargetRule\Model\Actions\Condition\Product\Special');
        $this->setValue(null);
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $conditions = array(
            array(
                'value' => 'Magento\TargetRule\Model\Actions\Condition\Product\Special\Price',
                'label' => __('Price (percentage)')
            )
        );

        return array(
            'value' => $conditions,
            'label' => __('Product Special')
        );
    }
}
