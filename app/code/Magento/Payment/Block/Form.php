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
 * Payment method form base block
 */
class Magento_Payment_Block_Form extends Magento_Core_Block_Template
{
    /**
     * Retrieve payment method model
     *
     * @return Magento_Payment_Model_Method_Abstract
     * @throws Magento_Core_Exception
     */
    public function getMethod()
    {
        $method = $this->getData('method');

        if (!($method instanceof Magento_Payment_Model_Method_Abstract)) {
            throw new Magento_Core_Exception(__('We cannot retrieve the payment method model object.'));
        }
        return $method;
    }

    /**
     * Retrieve payment method code
     *
     * @return string
     */
    public function getMethodCode()
    {
        return $this->getMethod()->getCode();
    }

    /**
     * Retrieve field value data from payment info object
     *
     * @param   string $field
     * @return  mixed
     */
    public function getInfoData($field)
    {
        return $this->escapeHtml($this->getMethod()->getInfoInstance()->getData($field));
    }

    /**
     * Check whether current payment method can create billing agreement
     *
     * @return bool
     */
    public function canCreateBillingAgreement()
    {
        return $this->getMethod()->canCreateBillingAgreement();
    }
}
