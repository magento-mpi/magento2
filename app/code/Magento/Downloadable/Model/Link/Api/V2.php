<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Downloadable links API model
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Magento_Downloadable_Model_Link_Api_V2 extends Magento_Downloadable_Model_Link_Api
{
    /**
     * Clean the object, leave only property values
     *
     * @param object $var
     * @return void
     */
    protected function _prepareData(&$var)
    {
        if (is_object($var)) {
            $var = get_object_vars($var);
            foreach ($var as $key => &$value) {
                $this->_prepareData($value);
            }
        }
    }

    /**
     * Add downloadable content to product
     *
     * @param int|string $productId
     * @param object $resource
     * @param string $resourceType
     * @param string|int $store
     * @param string $identifierType ('sku'|'id')
     * @return type
     */
    public function add($productId, $resource, $resourceType, $store = null, $identifierType = null)
    {
        $this->_prepareData($resource);
        return parent::add($productId, $resource, $resourceType, $store, $identifierType);
    }
}
