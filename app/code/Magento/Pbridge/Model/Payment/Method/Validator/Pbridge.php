<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Pbridge\Model\Payment\Method\Validator;

use Magento\Payment\Model\Method\ValidatorInterface;
use Magento\Payment\Model\Method\Validator;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Framework\Model\Exception;

/**
 * Class Pbridge
 * @package Magento\Pbridge\Model\Payment\Method\Validator
 */
class Pbridge implements ValidatorInterface
{
    /**
     * @var Validator\Method
     */
    private $_paymentValidator;

    /**
     * @param Validator\Method $paymentValidator
     */
    public function __construct(Validator\Method $paymentValidator)
    {
        $this->_paymentValidator = $paymentValidator;
    }

    /**
     * Validates payment method
     *
     * @param AbstractMethod $paymentMethod
     * @throws Exception
     * @return void
     */
    public function validate(AbstractMethod $paymentMethod)
    {
        $this->_paymentValidator->validate($paymentMethod);

        /** @todo place getPbridgeResponse to separate model */
        if (!$paymentMethod->getPbridgeResponse('token')) {
            throw new Exception(__("We can't find the Payment Bridge authentication data."));
        }
    }
}
