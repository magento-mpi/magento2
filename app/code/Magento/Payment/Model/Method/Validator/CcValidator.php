<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Payment\Model\Method\Validator;

use Magento\Payment\Model\Method\ValidatorInterface;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Payment\Model\Method\Validator;
use Magento\Framework\Model\Exception;

/**
 * Class CcValidator
 * @package Magento\Payment\Model\Method\Validator
 */
class CcValidator implements ValidatorInterface
{
    /**
     * @var Validator\Method
     */
    private $_paymentValidator;

    /**
     * List of Method\ValidatorInterface. Sorted in validation order
     * @var array
     */
    private $_validatorsList = [];

    /**
     * @param Method $paymentValidator
     * @param array $validatorsList
     */
    public function __construct(Validator\Method $paymentValidator, array $validatorsList = [])
    {
        $this->_paymentValidator = $paymentValidator;
        $this->_validatorsList = $validatorsList;
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

        foreach ($this->_validatorsList as $validator) {
            $validator->validate($paymentMethod);
        }
    }
}
