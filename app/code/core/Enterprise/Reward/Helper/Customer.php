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
 * @package     Enterprise_Reward
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Reward Helper for operations with customer
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Helper_Customer extends Mage_Core_Helper_Abstract
{
    /**
     * Return Unsubscribe notification URL
     *
     * @param string $notification Notification type
     * @return string
     */
    public function getUnsubscribeUrl($notification = false)
    {
        $params = array();
        if ($notification) {
            $params = array('notification' => $notification);
        }
        return Mage::getUrl('enterprise_reward/customer/unsubscribe/', array('notification' => $notification));
    }

    /**
     * Unsubscribe customer from notifications
     *
     * @param Mage_Customer_Model_Customer $customer Customer model
     * @param string $notification Notification type
     * @return Enterprise_Reward_Helper_Customer
     */
    public function unsibscribeCustomer($customer, $notification)
    {
        $attributeCode = false;
        if ($notification == 'update') {
            $attributeCode = 'reward_update_notification';
        } elseif ($notification == 'warning') {
            $attributeCode = 'reward_warning_notification';
        }

        if ($attributeCode) {
            $customer->setData($attributeCode, 0)
                ->getResource()
                ->saveAttribute($customer, $attributeCode);
        }

        return $this;
    }
}
