<?php
/**
 * Plugin for cart product configuration
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Model\Product\CartConfiguration\Plugin;

use Magento\Code\Plugin\InvocationChain;

class Configurable
{
    /**
     * Decide whether product has been configured for cart or not
     *
     * @param array $arguments
     * @param InvocationChain $invocationChain
     * @return bool
     */
    public function aroundIsProductConfigured(array $arguments, InvocationChain $invocationChain)
    {
        /** @var $product \Magento\Catalog\Model\Product */
        list($product, $config) = $arguments;

        if ($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            return isset($config['super_attribute']);
        }

        return $invocationChain->proceed($arguments);
    }
}
