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
 * @package     Mage_DirectPayment
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_DirectPayment_Model_Session extends Mage_Core_Model_Session_Abstract
{
    /**
     * Class constructor. Initialize session namespace
     */
    public function __construct()
    {
        $this->init('directpayment');
    }

    public function addCheckoutOrderIncrementId($orderId)
    {
        $orderIds = $this->getDirectPaymentOrderIncrementIds();
        if (!$orderIds) {
            $orderIds = array();
        }
        $orderIds[$orderId] = 1;
        $this->setDirectPaymentOrderIncrementIds($orderIds);
    }
    
    public function removeCheckoutOrderIncrementId($orderId)
    {
        $orderIds = $this->getDirectPaymentOrderIncrementIds();
        if (!$orderIds) {
            $orderIds = array();
        }
        elseif (!empty($orderIds[$orderId])){
            unset($orderIds[$orderId]);
        }
        $this->setDirectPaymentOrderIncrementIds($orderIds);
    }
    
    public function isCheckoutOrderIncrementIdExist($orderId)
    {
        $orderIds = $this->getDirectPaymentOrderIncrementIds();
        if (is_array($orderIds) && !empty($orderIds[$orderId])) {
            return true;
        }
        return false;
    }
}
