<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Bundle helper
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Bundle\Helper;

class Data extends \Magento\Core\Helper\AbstractHelper
{
    const XML_NODE_BUNDLE_PRODUCT_TYPE      = 'global/catalog/product/type/bundle';

    /**
     * Retrieve array of allowed product types for bundle selection product
     *
     * @return array
     */
    public function getAllowedSelectionTypes()
    {
        $config = \Mage::getConfig()->getNode(self::XML_NODE_BUNDLE_PRODUCT_TYPE);
        return array_keys($config->allowed_selection_types->asArray());
    }
}
