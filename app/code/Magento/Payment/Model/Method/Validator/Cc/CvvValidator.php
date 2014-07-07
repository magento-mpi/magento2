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
 * Class CvvValidator
 * @package Magento\Payment\Model\Method\Validator\Cc
 */
class CvvValidator implements ValidatorInterface
{
    /**
     * List of regexp for cvv2 type-based verification
     *
     * @var array
     */
    private $_verificationExpList = [
        'VI' => '/^[0-9]{3}$/',
        'MC' => '/^[0-9]{3}$/',
        'AE' => '/^[0-9]{4}$/',
        'DI' => '/^[0-9]{3}$/',
        'SS' => '/^[0-9]{3,4}$/',
        'SM' => '/^[0-9]{3,4}$/',
        'SO' => '/^[0-9]{3,4}$/',
        'OT' => '/^[0-9]{3,4}$/',
        'JCB' => '/^[0-9]{3,4}$/'
    ];

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
        $usecvv = $this->getConfigData('useccv');

        if (is_null($usecvv) || (bool)$usecvv) {
            $regExp = isset($this->_verificationExpList[$info->getCcType()])
                ? $this->_verificationExpList[$info->getCcType()]
                : '';
            if (!$info->getCcCid() || !$regExp || !preg_match($regExp, $info->getCcCid())) {
                throw new Exception(__('Please enter a valid credit card verification number.'));
            }
        }
    }
}
