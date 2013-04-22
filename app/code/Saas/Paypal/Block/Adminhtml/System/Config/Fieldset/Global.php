<?php
/**
 * Magento Saas Edition
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
 * @category    Saas
 * @package     Saas_Paypal
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

//class Saas_Paypal_Block_Adminhtml_System_Config_Fieldset_Global
//    extends Mage_Paypal_Block_Adminhtml_System_Config_Fieldset_Global
//{
//    /**
//     * Custom template
//     *
//     * @var string
//     */
//    protected $_template = 'saas/paypal/system/config/fieldset/global.phtml';
//
//    /**
//     * Getter for config element item
//     *
//     * @param  string|Varien_Data_Form_Element_Abstract $element
//     * @param  string $itemKey
//     * @return bool|array
//     */
//    public function getElementConfigItem($element, $itemKey)
//    {
//        if (is_string($element)) {
//            $element = $this->getElement($element);
//        }
//        if (!($element instanceof Varien_Data_Form_Element_Abstract)) {
//            return false;
//        }
//
//        $item = $element->getFieldConfig()->{$itemKey};
//        if (!$item) {
//            return false;
//        }
//        return $item->asCanonicalArray();
//    }
//
//    /**
//     * Getter for config element item in json format
//     *
//     * @param  string|Varien_Data_Form_Element_Abstract $element
//     * @param  string $itemKey
//     * @return string
//     */
//    public function getElementConfigItemJson($element, $itemKey)
//    {
//        return Mage::helper('core')->jsonEncode($this->getElementConfigItem($element, $itemKey));
//    }
//}
