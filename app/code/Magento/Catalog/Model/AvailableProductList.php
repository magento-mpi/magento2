<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model;

class AvailableProductList
{
    /**
     * @var \Magento\Catalog\Model\ProductTypes\ConfigInterface
     */
    protected $productTypesConfig;

    /**
     * @var string
     */
    protected $customAttributeName;

    /**
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypesConfig
     */
    public function __construct(\Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypesConfig)
    {
        $this->productTypesConfig = $productTypesConfig;
    }

    /**
     * Get available types
     *
     * @param string $customAttributeName
     *
     * @return array
     */
    public function getItem($customAttributeName)
    {
        $availableProductTypes = array();
        foreach ($this->productTypesConfig->getAll() as $type) {
            if (!isset($type['custom_attributes'][$customAttributeName])
                || $type['custom_attributes'][$customAttributeName] == 'true') {
                $availableProductTypes[] = $type['name'];
            }
        }
        return $availableProductTypes;
    }
}