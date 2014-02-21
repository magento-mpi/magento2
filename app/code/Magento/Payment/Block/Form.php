<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Payment\Block;

/**
 * Payment method form base block
 */
class Form extends \Magento\View\Element\Template
{
    /**
     * Retrieve payment method model
     *
     * @return \Magento\Payment\Model\Method
     * @throws \Magento\Core\Exception
     */
    public function getMethod()
    {
        $method = $this->getData('method');

        if (!($method instanceof \Magento\Payment\Model\Method)) {
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
}
