<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Model\Plugin;

class BundleLoadOptions
{
    /**
     * @var \Magento\Bundle\Model\Product\OptionList
     */
    protected $productOptionList;

    /**
     * @var \Magento\Framework\Api\AttributeDataBuilder
     */
    protected $customAttributeBuilder;

    /**
     * @param \Magento\Bundle\Model\Product\OptionList $productOptionList
     * @param \Magento\Framework\Api\AttributeDataBuilder $customAttributeBuilder
     */
    public function __construct(
        \Magento\Bundle\Model\Product\OptionList $productOptionList,
        \Magento\Framework\Api\AttributeDataBuilder $customAttributeBuilder
    ) {
        $this->productOptionList = $productOptionList;
        $this->customAttributeBuilder = $customAttributeBuilder;
    }

    /**
     * @param \Magento\Catalog\Model\Product $subject
     * @param callable $proceed
     * @param int $modelId
     * @param null $field
     * @return \Magento\Catalog\Model\Product
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundLoad(
        \Magento\Catalog\Model\Product $subject,
        \Closure $proceed,
        $modelId,
        $field = null
    ) {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $proceed($modelId, $field);
        if ($product->getTypeId() != \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
            return $product;
        }
        $customAttribute = $this->customAttributeBuilder
            ->setAttributeCode('bundle_product_options')
            ->setValue($this->productOptionList->getItems($product))
            ->create();
        $product->setData(
            'custom_attributes',
            array_merge($product->getCustomAttributes(), [$customAttribute])
        );
        return $product;
    }
}
