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
 * @category   Mage
 * @package    Mage_Paybox
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 *
 * Paybox Management Mode of intermediary page Dropdown source
 *
 * @category   Mage
 * @package    Mage_Paybox
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paybox_Model_Source_PaymentType
{
    public function toOptionArray()
    {
        return array(
            array('value' => '', 'label' => Mage::helper('paybox')->__('--Please Select--')),
            array('value' => Mage_Paybox_Model_System::PBX_PAYMENT_TYPE_CARTE, 'label' => Mage::helper('paybox')->__('CARTE')),
            array('value' => Mage_Paybox_Model_System::PBX_PAYMENT_TYPE_SYMPASS, 'label' => Mage::helper('paybox')->__('SYMPASS')),
            array('value' => Mage_Paybox_Model_System::PBX_PAYMENT_TYPE_PAYNOVA , 'label' => Mage::helper('paybox')->__('PAYNOVA')),
            array('value' => Mage_Paybox_Model_System::PBX_PAYMENT_TYPE_TERMINEO, 'label' => Mage::helper('paybox')->__('TERMINEO')),
            array('value' => Mage_Paybox_Model_System::PBX_PAYMENT_TYPE_PAYPAL, 'label' => Mage::helper('paybox')->__('PAYPAL')),
        );
    }
}