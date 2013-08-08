<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Billing Agreement abstaract class
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Payment_Model_Billing_AgreementAbstract extends Magento_Core_Model_Abstract
{
    /**
     * Payment method instance
     *
     * @var Mage_Payment_Model_Method_Abstract
     */
    protected $_paymentMethodInstance = null;

    /**
     * Billing Agreement Errors
     *
     * @var array
     */
    protected $_errors = array();

    /**
     * Init billing agreement
     *
     */
    abstract public function initToken();

    /**
     * Verify billing agreement details
     *
     */
    abstract public function verifyToken();

    /**
     * Create billing agreement
     *
     */
    abstract public function place();

    /**
     * Cancel billing agreement
     *
     */
    abstract public function cancel();

    /**
     * Retreive payment method instance
     *
     * @return Mage_Payment_Model_Method_Abstract
     */
    public function getPaymentMethodInstance()
    {
        if (is_null($this->_paymentMethodInstance)) {
            $this->_paymentMethodInstance = Mage::helper('Mage_Payment_Helper_Data')->getMethodInstance($this->getMethodCode());
        }
        if ($this->_paymentMethodInstance) {
            $this->_paymentMethodInstance->setStore($this->getStoreId());
        }
        return $this->_paymentMethodInstance;
    }

    /**
     * Validate data before save
     *
     * @return bool
     */
    public function isValid()
    {
        $this->_errors = array();
        if (is_null($this->getPaymentMethodInstance()) || !$this->getPaymentMethodInstance()->getCode()) {
            $this->_errors[] = Mage::helper('Mage_Payment_Helper_Data')->__('The payment method code is not set.');
        }
        if (!$this->getReferenceId()) {
            $this->_errors[] = Mage::helper('Mage_Payment_Helper_Data')->__('The reference ID is not set.');
        }
        return empty($this->_errors);
    }

    /**
     * Before save, it's overriden just to make data validation on before save event
     *
     * @throws Magento_Core_Exception
     * @return Magento_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        if ($this->isValid()) {
            return parent::_beforeSave();
        }
        array_unshift($this->_errors, Mage::helper('Mage_Payment_Helper_Data')->__('Unable to save Billing Agreement:'));
        throw new Magento_Core_Exception(implode(' ', $this->_errors));
    }
}
