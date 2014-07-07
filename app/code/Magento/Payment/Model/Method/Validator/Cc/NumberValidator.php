<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Payment\Model\Method\Validator\Cc;

use Magento\Payment\Model\Method\ValidatorInterface;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Framework\Model\Exception;

/**
 * Class NumberValidator
 * @package Magento\Payment\Model\Method\Validator\Cc
 */
class NumberValidator implements ValidatorInterface
{
    /**
     * Validates payment method
     *
     * @param AbstractMethod $paymentMethod
     * @throws \Magento\Framework\Model\Exception
     * @return void
     */
    public function validate(AbstractMethod $paymentMethod)
    {
        $info = $paymentMethod->getInfoInstance();
        $availableTypes = explode(',', $paymentMethod->getConfigData('cctypes'));

        $ccNumber = $info->getCcNumber();

        // remove credit card number delimiters such as "-" and space
        $ccNumber = preg_replace('/[\-\s]+/', '', $ccNumber);
        $info->setCcNumber($ccNumber);

        if (!in_array($info->getCcType(), $availableTypes)) {
            throw new Exception(__('Credit card type is not allowed for this payment method.'));
        }
    }
}
