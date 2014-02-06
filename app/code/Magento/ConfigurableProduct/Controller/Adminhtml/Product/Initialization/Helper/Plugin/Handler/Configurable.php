<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Controller\Adminhtml\Product\Initialization\Helper\Plugin\Handler;

class Configurable
{
    /**
     * Handle data received from Associated Products tab of configurable product
     *
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return void
     */
    public function aroundHandle(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = &$arguments[0];

        if ($product->getTypeId() != \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $invocationChain->proceed($arguments);
            return;
        }

        /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable $type */
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
                $id = $data['attribute_id'];
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

        $invocationChain->proceed($arguments);
    }
} 
