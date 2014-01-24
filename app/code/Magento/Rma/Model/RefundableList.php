<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * List of refundable products model
 */
namespace Magento\Rma\Model;

class RefundableList
{
    /**
     * @var \Magento\Catalog\Model\ProductTypes\Config
     */
    protected $productTypesConfig;

    /**
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypesConfig
     */
    public function __construct(\Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypesConfig)
    {
        $this->productTypesConfig = $productTypesConfig;
    }

    /**
     * Get refundable product types
     *
     * @return array
     */
    public function getItem()
    {
        $all = $this->productTypesConfig->getAll();
        $availableProductTypes = array();

        foreach ($all as $type) {
            if (isset($type['custom_attributes']['refundable']) && $type['custom_attributes']['refundable'] == 'true') {
                $availableProductTypes[] = $type['name'];
            }
        }
        return $availableProductTypes;
    }
}

