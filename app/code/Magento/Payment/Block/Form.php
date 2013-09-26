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
namespace Magento\Payment\Block;

class Form extends \Magento\Core\Block\Template
{
    /**
     * Retrieve payment method model
     *
     * @return \Magento\Payment\Model\Method\AbstractMethod
     * @throws \Magento\Core\Exception
     */
    public function getMethod()
    {
        $method = $this->getData('method');

        if (!($method instanceof \Magento\Payment\Model\Method\AbstractMethod)) {
            throw new \Magento\Core\Exception(__('We cannot retrieve the payment method model object.'));
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
