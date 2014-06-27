<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Payment\Model\Method\Validator;

use \Magento\Payment\Model\Method;

class Factory
{
    /**
     * List of Method\ValidatorInterface. [method_code => ValidatorInterface, ...]
     * @var array
     */
    private $_validatorsList = [];

    /**
     * @var \Magento\Framework\ObjectManager
     */
    private $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param array $validatorsList
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager, array $validatorsList = [])
    {
        $this->_objectManager = $objectManager;
        $this->_validatorsList = $validatorsList;
    }

    /**
     * @param Method\AbstractMethod $paymentMethod
     * @return null|Method\ValidatorInterface
     */
    public function create(Method\AbstractMethod $paymentMethod)
    {
        if (!isset($this->_validatorsList[$paymentMethod->getCode()])) {
            return null;
        }

        return $this->_objectManager->create($this->_validatorsList[$paymentMethod->getCode()]);
    }
}