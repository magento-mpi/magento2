<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PricePermissions\Controller\Adminhtml\Product\Initialization\Helper\Plugin\Handler\ProductType;

use Magento\PricePermissions\Controller\Adminhtml\Product\Initialization\Helper\Plugin\HandlerInterface;

class Configurable implements HandlerInterface
{
    /**
     * Handle data received from Associated Products tab of configurable product
     *
     * @param \Magento\Catalog\Model\Product $product
     */
    public function handle(\Magento\Catalog\Model\Product $product)
    {
        if ($product->getTypeId() != \Magento\Catalog\Model\Product\Type::TYPE_CONFIGURABLE) {
            return;
        }

        /** @var \Magento\Catalog\Model\Product\Type\Configurable $type */
        $type = $product->getTypeInstance();
        $originalAttributes = $type->getConfigurableAttributesAsArray($product);
        // Organize main information about original product attributes in assoc array form
        $originalAttributesMainInfo = array();
        if (is_array($originalAttributes)) {
            foreach ($originalAttributes as $originalAttribute) {
                $originalAttributesMainInfo[$originalAttribute['id']] = array();
                foreach ($originalAttribute['values'] as $value) {
                    $originalAttributesMainInfo[$originalAttribute['id']][$value['value_index']] = array(
                        'is_percent'    => $value['is_percent'],
                        'pricing_value' => $value['pricing_value']
                    );
                }
            }
        }
        $attributeData = $product->getConfigurableAttributesData();
        if (is_array($attributeData)) {
            foreach ($attributeData as &$data) {
                $id = $data['id'];
                foreach ($data['values'] as &$value) {
                    $valueIndex = $value['value_index'];
                    if (isset($originalAttributesMainInfo[$id][$valueIndex])) {
                        $value['pricing_value'] =
                            $originalAttributesMainInfo[$id][$valueIndex]['pricing_value'];
                        $value['is_percent'] = $originalAttributesMainInfo[$id][$valueIndex]['is_percent'];
                    } else {
                        $value['pricing_value'] = 0;
                        $value['is_percent'] = 0;
                    }
                }
            }
            $product->setConfigurableAttributesData($attributeData);
        }
    }
} 
