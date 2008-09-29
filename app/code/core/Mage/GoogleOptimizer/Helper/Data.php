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
class Mage_Googleoptimizer_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ENABLED = 'google/optimizer/active';

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
            || !$product->getGoogleOptimizerCodes()
            || !$product->getGoogleOptimizerCodes()->getControlScript()) {
            return $attributeHtml;
        }

        $attributeHtml = '<script>utmx_section("product_'.$attributeName.'")</script>' . $attributeHtml . '</noscript>';
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
            || !$category->getGoogleOptimizerCodes()
            || !$category->getGoogleOptimizerCodes()->getControlScript()) {
            return $attributeHtml;
        }

        $attributeHtml = '<script>utmx_section("category_'.$attributeName.'")</script>' . $attributeHtml . '</noscript>';
        return $attributeHtml;
    }
}