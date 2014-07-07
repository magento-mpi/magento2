<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Pbridge\Model\Payment\Method\Validator;

use Magento\Payment\Model\Method\ValidatorInterface;
use Magento\Payment\Model\Method\AbstractMethod;

/**
 * Class Method
 * @package Magento\Pbridge\Model\Payment\Method\Validator
 */
class Method implements ValidatorInterface
{
    /**
     * @var Pbridge
     */
    private $_pbridgeValidator;

    /**
     * @param Pbridge $pbridgeValidator
     */
    public function __construct(Pbridge $pbridgeValidator)
    {
        $this->_pbridgeValidator = $pbridgeValidator;
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
        /** @todo place getPbridgeMethodInstance to separate model */
        $this->_pbridgeValidator->validate($paymentMethod->getPbridgeMethodInstance());
    }
}
