<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Billing Agreement abstaract class
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Payment\Model\Billing;

abstract class AgreementAbstract extends \Magento\Core\Model\AbstractModel
{
    /**
     * Payment method instance
     *
     * @var \Magento\Payment\Model\Method\AbstractMethod
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
     * @return \Magento\Payment\Model\Method\AbstractMethod
     */
    public function getPaymentMethodInstance()
    {
        if (is_null($this->_paymentMethodInstance)) {
            $this->_paymentMethodInstance = \Mage::helper('Magento\Payment\Helper\Data')->getMethodInstance($this->getMethodCode());
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
            $this->_errors[] = __('The payment method code is not set.');
        }
        if (!$this->getReferenceId()) {
            $this->_errors[] = __('The reference ID is not set.');
        }
        return empty($this->_errors);
    }

    /**
     * Before save, it's overriden just to make data validation on before save event
     *
     * @throws \Magento\Core\Exception
     * @return \Magento\Core\Model\AbstractModel
     */
    protected function _beforeSave()
    {
        if ($this->isValid()) {
            return parent::_beforeSave();
        }
        array_unshift($this->_errors, __('Unable to save Billing Agreement:'));
        throw new \Magento\Core\Exception(implode(' ', $this->_errors));
    }
}
