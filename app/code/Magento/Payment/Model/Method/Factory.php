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
     * @param string $className
     * @param array $data
     * @return mixed
     * @throws Magento_Core_Exception
     */
    public function create($className, $data= array())
    {
        $method = $this->_objectManager->create($className, $data);
        if (!($method instanceof Magento_Payment_Model_Method_Abstract)) {
            throw new Magento_Core_Exception(sprintf("%s class doesn't extend Magento_Payment_Model_Method_Abstract",
                $className));
        }
        return $method;
    }
}
