<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Product tier price api V2
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Product_Attribute_Tierprice_Api_V2 extends Magento_Catalog_Model_Product_Attribute_Tierprice_Api
{
    /**
     *  Prepare tier prices for save
     *
     *  @param      Magento_Catalog_Model_Product $product
     *  @param      array $tierPrices
     *  @return     array
     */
    public function prepareTierPrices($product, $tierPrices = null)
    {
        if (!is_array($tierPrices)) {
            return null;
        }

        $updateValue = array();

        foreach ($tierPrices as $tierPrice) {
            if (!is_object($tierPrice)
                || !isset($tierPrice->qty)
                || !isset($tierPrice->price)) {
                $this->_fault('data_invalid', __('Invalid Tier Prices'));
            }

            if (!isset($tierPrice->website) || $tierPrice->website == 'all') {
                $tierPrice->website = 0;
            } else {
                try {
                    $tierPrice->website = Mage::app()->getWebsite($tierPrice->website)->getId();
                } catch (Magento_Core_Exception $e) {
                    $tierPrice->website = 0;
                }
            }

            if (intval($tierPrice->website) > 0 && !in_array($tierPrice->website, $product->getWebsiteIds())) {
                $this->_fault('data_invalid', __('We found invalid tier prices: the product is not associated with the requested website.'));
            }

            if (!isset($tierPrice->customer_group_id)) {
                $tierPrice->customer_group_id = 'all';
            }

            if ($tierPrice->customer_group_id == 'all') {
                $tierPrice->customer_group_id = Magento_Customer_Model_Group::CUST_GROUP_ALL;
            }

            $updateValue[] = array(
                'website_id' => $tierPrice->website,
                'cust_group' => $tierPrice->customer_group_id,
                'price_qty'  => $tierPrice->qty,
                'price'      => $tierPrice->price
            );

        }

        return $updateValue;
    }
}
