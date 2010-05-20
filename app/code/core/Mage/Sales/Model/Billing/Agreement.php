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
 * Billing Agreement model
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Sales_Model_Billing_Agreement extends Mage_Payment_Model_Billing_Agreement
{
    const STATUS_ACTIVE     = 'active';
    const STATUS_CANCELED   = 'canceled';

    /**
     * Init model
     *
     */
    protected function _construct()
    {
        $this->_init('sales/billing_agreement');
    }

    /**
     * Set created_at parameter
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        $date = Mage::getModel('core/date')->gmtDate();
        if ($this->isObjectNew()) {
            $this->setCreatedAt($date);
        } else {
            $this->setUpdatedAt($date);
        }
        return parent::_beforeSave();
    }

    /**
     * Retrieve billing agreement status label
     *
     * @return string
     */
    public function getStatusLabel()
    {
        switch ($this->getStatus()) {
            case self::STATUS_ACTIVE:
                $label = Mage::helper('sales')->__('Active');
                break;
            default:
                $label = Mage::helper('sales')->__('Canceled');
        }
        return $label;
    }

    /**
     * Init billing agreement
     *
     * @return Mage_Sales_Model_Billing_Agreement
     */
    public function initToken()
    {
        $this->getPaymentMethodInstance()
            ->initBillingAgreementToken($this);
        return $this->getRedirectUrl();
    }

    /**
     * Get billing agreement details
     * Data from response is inside this object
     *
     * @return Mage_Sales_Model_Billing_Agreement
     */
    public function verifyToken()
    {
        $this->getPaymentMethodInstance()
            ->getBillingAgreementTokenInfo($this);
        return $this;
    }

    /**
     * Create billing agreement
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return Mage_Sales_Model_Billing_Agreement
     */
    public function place(Mage_Customer_Model_Customer $customer)
    {
        $this->verifyToken();

        $this->getPaymentMethodInstance()
            ->placeBillingAgreement($this);

        $this->setCustomerId($customer->getId())
            ->setMethodCode($this->getMethodCode())
            ->setReferenceId($this->getBillingAgreementId())
            ->setStatus(self::STATUS_ACTIVE)
            ->save();
        return $this;
    }

    /**
     * Cancel billing agreement
     *
     * @return Mage_Sales_Model_Billing_Agreement
     */
    public function cancel()
    {
        $this->setStatus(self::STATUS_CANCELED);
        $this->getPaymentMethodInstance()->updateBillingAgreementStatus($this);
        return $this->save();
    }

    /**
     * Check whether can cancel billing agreement
     *
     * @return bool
     */
    public function canCancel()
    {
        return ($this->getStatus() != self::STATUS_CANCELED);
    }

}
