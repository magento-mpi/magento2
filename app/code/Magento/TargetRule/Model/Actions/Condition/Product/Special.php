<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TargetRule\Model\Actions\Condition\Product;

/**
 * TargetRule Action Special Product Attributes Condition Model
 *
 */
class Special extends \Magento\Rule\Model\Condition\Product\AbstractProduct
{
    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Backend\Helper\Data $backendData
     * @param \Magento\Eav\Model\Config $config
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Resource\Product $productResource
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Set\Collection $attrSetCollection
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Eav\Model\Config $config,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Resource\Product $productResource,
        \Magento\Eav\Model\Resource\Entity\Attribute\Set\Collection $attrSetCollection,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        array $data = array()
    ) {
        parent::__construct(
            $context,
            $backendData,
            $config,
            $productFactory,
            $productResource,
            $attrSetCollection,
            $localeFormat,
            $data
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

        return array('value' => $conditions, 'label' => __('Product Special'));
    }
}
