<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * RMA model
 */
namespace Magento\Rma\Model;

class RefundableList extends \Magento\Core\Model\AbstractModel
{
    /**
     * @param \Magento\Catalog\Model\ProductTypes\Config $productTypesConfig
     */
    public function __construct(\Magento\Catalog\Model\ProductTypes\Config $productTypesConfig)
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
            if (array_key_exists('custom_attributes', $type)
                && array_key_exists('refundable', $type['custom_attributes'])
                && 'true' == $type['custom_attributes']['refundable']
            ) {
                $availableProductTypes[] = $type['name'];
            }
        }
        return $availableProductTypes;
    }
}
