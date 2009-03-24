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
 * @package    Enterprise_GiftCard
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Enterprise_GiftCard_Block_Adminhtml_Sales_Items_Column_Name_Giftcard extends Mage_Adminhtml_Block_Sales_Items_Column_Name
{
    /**
     * Prepare custom option for display, returns false if there's no value
     *
     * @param string $code
     * @return mixed
     */
    protected function _prepareCustomOption($code)
    {
        if ($option = $this->getItem()->getProductOptionByCode($code)) {
            return nl2br($this->htmlEscape($option));
        }
        return false;
    }

    /**
     * Get gift card option list
     *
     * @return array
     */
    protected function _getGiftcardOptions()
    {
        $result = array();
        if ($value = $this->_prepareCustomOption('giftcard_sender_name')) {
            if ($email = $this->_prepareCustomOption('giftcard_sender_email')) {
                $value = "{$value} &lt;{$email}&gt;";
            }
            $result[] = array(
                'label'=>Mage::helper('enterprise_giftcard')->__('Sender'),
                'value'=>$value,
            );
        }
        if ($value = $this->_prepareCustomOption('giftcard_recipient_name')) {
            if ($email = $this->_prepareCustomOption('giftcard_recipient_email')) {
                $value = "{$value} &lt;{$email}&gt;";
            }
            $result[] = array(
                'label'=>Mage::helper('enterprise_giftcard')->__('Recipient'),
                'value'=>$value,
            );
        }
        if ($value = $this->_prepareCustomOption('giftcard_message')) {
            $result[] = array(
                'label'=>Mage::helper('enterprise_giftcard')->__('Message'),
                'value'=>$value,
            );
        }

        if ($value = $this->_prepareCustomOption('giftcard_lifetime')) {
            $result[] = array(
                'label'=>Mage::helper('enterprise_giftcard')->__('Lifetime'),
                'value'=>sprintf('%s days', $value),
            );
        }

        $yes = Mage::helper('enterprise_giftcard')->__('Yes');
        $no = Mage::helper('enterprise_giftcard')->__('No');
        if ($value = $this->_prepareCustomOption('giftcard_is_redeemable')) {
            $result[] = array(
                'label'=>Mage::helper('enterprise_giftcard')->__('Redeemable'),
                'value'=>($value ? $yes : $no),
            );
        }

        if ($codes = $this->getItem()->getProductOptionByCode('giftcard_created_codes')) {
            $result[] = array(
                'label'=>Mage::helper('enterprise_giftcard')->__('Codes'),
                'value'=>implode('<br />', $codes),
            );
        }
        return $result;
    }

    /**
     * Return gift card and custom options array
     *
     * @return array
     */
    public function getOrderOptions()
    {
        return array_merge($this->_getGiftcardOptions(), parent::getOrderOptions());
    }
}