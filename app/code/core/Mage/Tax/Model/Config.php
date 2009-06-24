<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Configuration pathes storage
 *
 * @category   Mage
 * @package    Mage_Tax
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tax_Model_Config
{
    // tax classes
    const CONFIG_XML_PATH_SHIPPING_TAX_CLASS = 'tax/classes/shipping_tax_class';

    // tax calculation
    const CONFIG_XML_PATH_PRICE_INCLUDES_TAX = 'tax/calculation/price_includes_tax';
    const CONFIG_XML_PATH_SHIPPING_INCLUDES_TAX = 'tax/calculation/shipping_includes_tax';
    const CONFIG_XML_PATH_BASED_ON = 'tax/calculation/based_on';
    CONST CONFIG_XML_PATH_APPLY_ON = 'tax/calculation/apply_tax_on';
    const CONFIG_XML_PATH_APPLY_AFTER_DISCOUNT = 'tax/calculation/apply_after_discount';
    const CONFIG_XML_PATH_DISCOUNT_TAX = 'tax/calculation/discount_tax';
    const XML_PATH_ALGORITHM = 'tax/calculation/algorithm';

    // tax defaults
    const CONFIG_XML_PATH_DEFAULT_COUNTRY = 'tax/defaults/country';
    const CONFIG_XML_PATH_DEFAULT_REGION = 'tax/defaults/region';
    const CONFIG_XML_PATH_DEFAULT_POSTCODE = 'tax/defaults/postcode';

    // tax display
    const CONFIG_XML_PATH_PRICE_DISPLAY_TYPE    = 'tax/display/type';
    const CONFIG_XML_PATH_DISPLAY_TAX_COLUMN    = 'tax/display/column_in_summary';
    const CONFIG_XML_PATH_DISPLAY_FULL_SUMMARY  = 'tax/display/full_summary';
    const CONFIG_XML_PATH_DISPLAY_SHIPPING      = 'tax/display/shipping';
    const CONFIG_XML_PATH_DISPLAY_ZERO_TAX      = 'tax/display/zero_tax';
    const XML_PATH_DISPLAY_TYPE_SUBTOTAL        = 'tax/display/subtotal';

    // deprecated settings
    const CONFIG_XML_PATH_SHOW_IN_CATALOG = 'tax/display/show_in_catalog';
    const CONFIG_XML_PATH_DEFAULT_PRODUCT_TAX_GROUP = 'catalog/product/default_tax_group';


    const CALCULATION_STRING_SEPARATOR = '|';

    const DISPLAY_TYPE_EXCLUDING_TAX = 1;
    const DISPLAY_TYPE_INCLUDING_TAX = 2;
    const DISPLAY_TYPE_BOTH = 3;

    /**
     * Get defined tax calculation agorithm
     *
     * @param   store $store
     * @return  string
     */
    public function getAlgorithm($store=null)
    {
        return Mage::getStoreConfig(self::XML_PATH_ALGORITHM, $store);
    }

    /**
     * Get tax class id specified for shipping tax estimation
     *
     * @param   store $store
     * @return  int
     */
    public function getShippingTaxClass($store=null)
    {
        return (int)Mage::getStoreConfig(self::CONFIG_XML_PATH_SHIPPING_TAX_CLASS, $store);
    }

    /**
     * Check configutation setting if we can display subtotal including tax
     *
     * @param   store $store
     * @return  bool
     */
    public function displaySubtotalExcludingTax($store=null)
    {
        $res = (int)Mage::getStoreConfig(self::XML_PATH_DISPLAY_TYPE_SUBTOTAL, $store);
        return in_array(
            $res,
            array(Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX, Mage_Tax_Model_Config::DISPLAY_TYPE_BOTH)
        );
    }

    /**
     * Check configutation setting if we can display subtotal including tax
     *
     * @param   store $store
     * @return  bool
     */
    public function displaySubtotalIncludingTax($store=null)
    {
        $res = (int)Mage::getStoreConfig(self::XML_PATH_DISPLAY_TYPE_SUBTOTAL, $store);
        return in_array(
            $res,
            array(Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX, Mage_Tax_Model_Config::DISPLAY_TYPE_BOTH)
        );
    }

    /**
     * Check configuration settigs if we can display subtotal including and excluding tax together
     *
     * @param   store $store
     * @return  bool
     */
    public function displaySubtotalBoth($store=null)
    {
        $res = (int)Mage::getStoreConfig(self::XML_PATH_DISPLAY_TYPE_SUBTOTAL, $store);
        return $res == Mage_Tax_Model_Config::DISPLAY_TYPE_BOTH;
    }
}
