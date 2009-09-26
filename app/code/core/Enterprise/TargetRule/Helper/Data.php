<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * TargetRule data helper
 *
 * @category   Enterprise
 * @package    Enterprise_TargetRule
 */
class Enterprise_TargetRule_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_TARGETRULE_CONFIG    = 'catalog/enterprise_targetrule/';

    /**
     * Retrieve Maximum Number of Products in Product List
     *
     * @param int $type product list type
     * @throws Mage_Core_Exception
     * @return int
     */
    public function getMaximumNumberOfProduct($type)
    {
        switch ($type) {
            case Enterprise_TargetRule_Model_Rule::RELATED_PRODUCTS:
                $number = Mage::getStoreConfig(self::XML_PATH_TARGETRULE_CONFIG . 'related_rule_based_positions');
                break;
            case Enterprise_TargetRule_Model_Rule::UP_SELLS:
                $number = Mage::getStoreConfig(self::XML_PATH_TARGETRULE_CONFIG . 'upsell_rule_based_positions');
                break;
            case Enterprise_TargetRule_Model_Rule::CROSS_SELLS:
                $number = Mage::getStoreConfig(self::XML_PATH_TARGETRULE_CONFIG . 'crosssell_rule_based_positions');
                break;
            default:
                Mage::throwException(Mage::helper('enterprise_targetrule')->__('Invalid product list type'));
        }

        return $number;
    }

    /**
     * Show Related/Upsell/Cross-Sell Products
     *
     * @param int $type
     * @throws Mage_Core_Exception
     * @return int
     */
    public function getShowProducts($type)
    {
        switch ($type) {
            case Enterprise_TargetRule_Model_Rule::RELATED_PRODUCTS:
                $show = Mage::getStoreConfig(self::XML_PATH_TARGETRULE_CONFIG . 'related_position_behavior');
                break;
            case Enterprise_TargetRule_Model_Rule::UP_SELLS:
                $show = Mage::getStoreConfig(self::XML_PATH_TARGETRULE_CONFIG . 'upsell_position_behavior');
                break;
            case Enterprise_TargetRule_Model_Rule::CROSS_SELLS:
                $show = Mage::getStoreConfig(self::XML_PATH_TARGETRULE_CONFIG . 'crosssell_position_behavior');
                break;
            default:
                Mage::throwException(Mage::helper('enterprise_targetrule')->__('Invalid product list type'));
        }

        return $show;
    }
}
