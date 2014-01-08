<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Helper\Product\Configuration\Plugin;

class Grouped 
{
    /**
     * Retrieves grouped product options list
     *
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return mixed
     */
    public function aroundGetOptions(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        /** @var \Magento\Catalog\Model\Product\Configuration\Item\ItemInterface $item */
        $item = $arguments['item'];
        $product = $item->getProduct();
        $typeId  = $product->getTypeId();
        if ($typeId == \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE) {
            $options = array();
            /** @var \Magento\GroupedProduct\Model\Product\Type\Grouped $typeInstance */
            $typeInstance = $product->getTypeInstance();
            $associatedProducts = $typeInstance->getAssociatedProducts($product);

            if ($associatedProducts) {
                foreach ($associatedProducts as $associatedProduct) {
                    $qty = $item->getOptionByCode('associated_product_' . $associatedProduct->getId());
                    $option = array(
                        'label' => $associatedProduct->getName(),
                        'value' => ($qty && $qty->getValue()) ? $qty->getValue() : 0
                    );
                    $options[] = $option;
                }
            }

            $options = array_merge($options, $invocationChain->proceed($arguments));
            $isUnConfigured = true;
            foreach ($options as &$option) {
                if ($option['value']) {
                    $isUnConfigured = false;
                    break;
                }
            }
            return $isUnConfigured ? array() : $options;
        }
        return $invocationChain->proceed($arguments);
    }
} 
