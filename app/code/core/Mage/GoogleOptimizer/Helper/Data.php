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
 * @package    Mage_GoogleOptimizer
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Google Optimizer Data Helper
 *
 * @category   Mage
 * @package    Mage_GoogleOptimizer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleOptimizer_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ENABLED = 'google/optimizer/active';
    const XML_PATH_ALLOWED_ATTRIBUTES = 'admin/attributes';

    public function isOptimizerActive()
    {
        return Mage::app()->getStore()->getConfig(self::XML_PATH_ENABLED);
    }

    /**
     * Prepare product attribute html output
     *
     * @param unknown_type $callObject
     * @param unknown_type $attributeHtml
     * @param unknown_type $params
     * @return unknown
     */
    public function productAttribute($callObject, $attributeHtml, $params)
    {
        $attributeName  = $params['attribute'];
        $product        = $params['product'];

        if (!$this->isOptimizerActive()
            || !$product->getGoogleOptimizerScripts()
            || !$product->getGoogleOptimizerScripts()->getControlScript()) {
            return $attributeHtml;
        }
        if (in_array($attributeName, $product->getGoogleOptimizerScripts()->getAttributes())) {
            $attributeHtml = '<script>utmx_section("product_'.$attributeName.'_'.$product->getId().'")</script>' . $attributeHtml . '</noscript>';
        }
        return $attributeHtml;
    }

    /**
     * Prepare category attribute html output
     *
     * @param unknown_type $callObject
     * @param unknown_type $attributeHtml
     * @param unknown_type $params
     * @return unknown
     */
    public function categoryAttribute($callObject, $attributeHtml, $params)
    {
        $attributeName  = $params['attribute'];
        $category       = $params['category'];

        if (!$this->isOptimizerActive()
            || !$category->getGoogleOptimizerScripts()
            || !$category->getGoogleOptimizerScripts()->getControlScript()) {
            return $attributeHtml;
        }

        $attributeHtml = '<script>utmx_section("category_'.$attributeName.'_'.$category->getId().'")</script>' . $attributeHtml . '</noscript>';
        return $attributeHtml;
    }

    /**
     * Return conversion pages from source model
     *
     * @return Varien_Object
     */
    public function getConversionPagesUrl()
    {
        /**
         * Example:
         *
         * array(
         *  'checkout_cart' => 'http://base.url/...'
         * )
         */
        $urls = array();
        $choices = Mage::getModel('googleoptimizer/adminhtml_system_config_source_googleoptimizer_conversionpages')
            ->toOptionArray();
        foreach ($choices as $choice) {
            $route = '';
            switch ($choice['value']) {
                case 'checkout_cart':
                    $route = 'checkout/cart';
                    break;
                case 'checkout_onepage':
                    $route = 'checkout/onepage';
                    break;
                case 'checkout_multishipping':
                    $route = 'checkout/multishipping';
                    break;
                case 'checkout_onepage_success':
                    $route = 'checkout/onepage/success/';
                    break;
                case 'checkout_multishipping_success':
                    $route = 'checkout/multishipping/success/';
                    break;
                case 'customer_account_create':
                    $route = 'customer/account/create/';
                    break;
            }
            if ($route) {
                $urls[$choice['value']] = $this->_getUrl($route, array('_secure' => true));
            }
        }
        return new Varien_Object($urls);
    }


    /**
     * Create array of attributes for variation
     * that allowed by googleoptimizer config
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getProductAttributes($product)
    {
        /** @var $product Mage_Catalog_Model_Product */
        $allowedAttributes = array_keys(Mage::getConfig()->getNode(self::XML_PATH_ALLOWED_ATTRIBUTES)->asArray());
        $productAttributes = $product->getAttributes();
        $optimizerAttributes = array();
        foreach ($productAttributes as $_attributeCode => $_attribute) {
            if (in_array($_attributeCode, $allowedAttributes)) {
                $optimizerAttributes[] = array(
                    'label' => $_attribute->getFrontendLabel(),
                    'value' => $_attributeCode
                );
            }
        }
        return $optimizerAttributes;
    }
}