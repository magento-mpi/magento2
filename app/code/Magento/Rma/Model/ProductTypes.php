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

class ProductTypes extends \Magento\Core\Model\AbstractModel
{

    public function __construct(\Magento\Catalog\Model\ProductTypes\Config $productTypesConfig)
    {
        $this->productTypesConfig = $productTypesConfig;
        $this->filterTypes();
    }

    /**
     * Filter and save all product types by "refundable" custom attribute
     */
    protected function filterTypes()
    {
        $all = $this->productTypesConfig->getAll();
        $this->_availableProductTypes = array();

        foreach ($all as $type) {
            if (array_key_exists('custom_attributes', $type)
                && array_key_exists('refundable', $type['custom_attributes'])
                && 'true' == $type['custom_attributes']['refundable']
            ) {
                $this->_availableProductTypes[] = $type['name'];
            }
        }
    }

    /**
     * Get refundable product types
     *
     * @return array
     */
    public function getRefundableTypes()
    {
        return $this->_availableProductTypes;
    }

}