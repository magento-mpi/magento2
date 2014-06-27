<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Payment\Model\Method\Validator;

use Magento\Payment\Model\Method\ValidatorInterface;
use Magento\Payment\Model\Method\Context;
use Magento\Payment\Model\Method\AbstractMethod;

/**
 * Class Method
 * @package Magento\Payment\Model\Method\Validator
 */
class Method implements ValidatorInterface
{

    /**
     * @var Context\AdapterFactory
     */
    private $_adapterFactory;

    /**
     * @param Context\AdapterFactory $adapterFactory
     */
    public function __construct(Context\AdapterFactory $adapterFactory)
    {
        $this->_adapterFactory = $adapterFactory;
    }

    /**
     * Validates payment method
     *
     * @param AbstractMethod $paymentMethod
     * @throws \Magento\Framework\Model\Exception
     * @return void
     */
    public function validate(AbstractMethod $paymentMethod)
    {
        $contextAdapter = $this->_adapterFactory->create($paymentMethod);

        if (!$paymentMethod->canUseForCountry($contextAdapter->getCountryId())) {
            throw new \Magento\Framework\Model\Exception(
                __('You can\'t use the payment type you selected to make payments to the billing country.')
            );
        }
    }
}
