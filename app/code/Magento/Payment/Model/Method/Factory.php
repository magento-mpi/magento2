<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class Magento_Payment_Model_Method_Factory
 */
class Magento_Payment_Model_Method_Factory
{
    /**
     * Object manager
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Construct
     *
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Creates new instances of payment method models
     *
     * @param $className
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function create($className)
    {
        $method = $this->_objectManager->create($className);
        if (!($method instanceof Magento_Payment_Model_Method_Abstract)) {
            throw new InvalidArgumentException(sprintf("Payment method has wrong type",
                $className));
        }
        return $method;
    }
}
