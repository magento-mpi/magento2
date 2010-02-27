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
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Flat sales order payment resource
 *
 */
class Mage_Sales_Model_Mysql4_Order_Payment extends Mage_Sales_Model_Mysql4_Order_Abstract
{
    protected $_eventPrefix = 'sales_order_payment_resource';

    protected function _construct()
    {
        $this->_init('sales/order_payment', 'entity_id');
    }

    /**
     * Also serialize additional information
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return Mage_Sales_Model_Mysql4_Order_Payment
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $payment)
    {
        $additionalInformation = $payment->getData('additional_information');
        if (empty($additionalInformation)) {
            $payment->setData('additional_information', null);
        } elseif (is_array($additionalInformation)) {
            $payment->setData('additional_information', serialize($additionalInformation));
        }
        parent::_beforeSave($payment);
        return $this;
    }

    /**
     * Unserialize additional information after loading the object
     *
     * @param Varien_Object $payment
     * @return Mage_Sales_Model_Mysql4_Order_Payment
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $payment)
    {
        $this->unserializeFields($payment);
        parent::_afterLoad($payment);
        return $this;
    }

    /**
     * Unserialize additional information after saving the object
     *
     * @param Varien_Object $payment
     * @return Mage_Sales_Model_Mysql4_Order_Payment
     */
    protected function _afterSave(Mage_Core_Model_Abstract $payment)
    {
        $this->unserializeFields($payment);
        parent::_afterSave($payment);
        return $this;
    }

    /**
     * Unserialize additional data if required
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return void
     */
    public function unserializeFields(Mage_Sales_Model_Order_Payment $payment)
    {
        $additionalInformation = $payment->getData('additional_information');
        if (empty($additionalInformation)) {
            $payment->setData('additional_information', array());
        } elseif (!is_array($additionalInformation)) {
            $payment->setData('additional_information', unserialize($additionalInformation));
        }
    }
}
