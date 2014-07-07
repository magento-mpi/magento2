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
 * Class NumberTypeValidator
 * @package Magento\Payment\Model\Method\Validator\Cc
 */
class NumberTypeValidator implements ValidatorInterface
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

        $ccTypeRegExpList = array(
            //Solo, Switch or Maestro. International safe
            'SO' => '/(^(6334)[5-9](\d{11}$|\d{13,14}$))|(^(6767)(\d{12}$|\d{14,15}$))/',
            'SM' => '/(^(5[0678])\d{11,18}$)|(^(6[^05])\d{11,18}$)|(^(601)[^1]\d{9,16}$)|(^(6011)\d{9,11}$)' .
                '|(^(6011)\d{13,16}$)|(^(65)\d{11,13}$)|(^(65)\d{15,18}$)' .
                '|(^(49030)[2-9](\d{10}$|\d{12,13}$))|(^(49033)[5-9](\d{10}$|\d{12,13}$))' .
                '|(^(49110)[1-2](\d{10}$|\d{12,13}$))|(^(49117)[4-9](\d{10}$|\d{12,13}$))' .
                '|(^(49118)[0-2](\d{10}$|\d{12,13}$))|(^(4936)(\d{12}$|\d{14,15}$))/',
            // Visa
            'VI' => '/^4[0-9]{12}([0-9]{3})?$/',
            // Master Card
            'MC' => '/^5[1-5][0-9]{14}$/',
            // American Express
            'AE' => '/^3[47][0-9]{13}$/',
            // Discover
            'DI' => '/^(30[0-5][0-9]{13}|3095[0-9]{12}|35(2[8-9][0-9]{12}|[3-8][0-9]{13})' .
                '|36[0-9]{12}|3[8-9][0-9]{14}|6011(0[0-9]{11}|[2-4][0-9]{11}|74[0-9]{10}|7[7-9][0-9]{10}' .
                '|8[6-9][0-9]{10}|9[0-9]{11})|62(2(12[6-9][0-9]{10}|1[3-9][0-9]{11}|[2-8][0-9]{12}' .
                '|9[0-1][0-9]{11}|92[0-5][0-9]{10})|[4-6][0-9]{13}|8[2-8][0-9]{12})|6(4[4-9][0-9]{13}' .
                '|5[0-9]{14}))$/',
            // JCB
            'JCB' => '/^(30[0-5][0-9]{13}|3095[0-9]{12}|35(2[8-9][0-9]{12}|[3-8][0-9]{13})|36[0-9]{12}' .
                '|3[8-9][0-9]{14}|6011(0[0-9]{11}|[2-4][0-9]{11}|74[0-9]{10}|7[7-9][0-9]{10}' .
                '|8[6-9][0-9]{10}|9[0-9]{11})|62(2(12[6-9][0-9]{10}|1[3-9][0-9]{11}|[2-8][0-9]{12}' .
                '|9[0-1][0-9]{11}|92[0-5][0-9]{10})|[4-6][0-9]{13}|8[2-8][0-9]{12})|6(4[4-9][0-9]{13}' .
                '|5[0-9]{14}))$/'
        );
        $ccNumAndTypeMatches = isset($ccTypeRegExpList[$info->getCcType()])
            && preg_match($ccTypeRegExpList[$info->getCcType()], $info->getCcNumber());

        if (!$ccNumAndTypeMatches && !$info->getCcType() == 'OT') {
            throw new Exception(__('Credit card number mismatch with credit card type.'));
        }
    }
}
