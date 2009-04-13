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
 * @category   Enterprise
 * @package    Enterprise_GiftCardAccount
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Enterprise_GiftCardAccount_Block_Manage_Widget_Grid_Column_Renderer_Currency
extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Currency
{
    protected static $_websiteBaseCurrencyCodes = array();

    protected function _getCurrencyCode($row)
    {
        $websiteId = Mage::registry('current_giftcardaccount')->getWebsiteId();
        if (!isset(self::$_websiteBaseCurrencyCodes[$websiteId])) {
            self::$_websiteBaseCurrencyCodes[$websiteId] = Mage::app()->getWebsite($websiteId)->getBaseCurrencyCode();
        }
        return self::$_websiteBaseCurrencyCodes[$websiteId];
    }

    protected function _getRate($row)
    {
        return 1;
    }
}
