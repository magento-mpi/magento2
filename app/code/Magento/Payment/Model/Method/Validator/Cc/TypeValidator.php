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
 * Class TypeValidator
 * @package Magento\Payment\Model\Method\Validator\Cc
 */
class TypeValidator implements ValidatorInterface
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

        if (!($this->_validateCcNum($info->getCcNumber())
            || $info->getCcType() == 'OT' && preg_match('/^\\d+$/', $info->getCcNumber()))) {
            throw new Exception(__('Invalid Credit Card Number'));
        }
    }

    /**
     * Validate credit card number
     *
     * @param   string $ccNumber
     * @return  bool
     */
    private function _validateCcNum($ccNumber)
    {
        $cardNumber = strrev($ccNumber);
        $numSum = 0;

        for ($i = 0; $i < strlen($cardNumber); $i++) {
            $currentNum = substr($cardNumber, $i, 1);

            /**
             * Double every second digit
             */
            if ($i % 2 == 1) {
                $currentNum *= 2;
            }

            /**
             * Add digits of 2-digit numbers together
             */
            if ($currentNum > 9) {
                $firstNum = $currentNum % 10;
                $secondNum = ($currentNum - $firstNum) / 10;
                $currentNum = $firstNum + $secondNum;
            }

            $numSum += $currentNum;
        }
    }
}
