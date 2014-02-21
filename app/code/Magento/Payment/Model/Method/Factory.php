<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Payment\Model\Method;

/**
 * Class \Magento\Payment\Model\Method\Factory
 */
class Factory
{
    /**
     * Object manager
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * Construct
     *
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Creates new instances of payment method models
     *
     * @param string $className
     * @param array $data
     * @return \Magento\Payment\Model\Method
     * @throws \Magento\Core\Exception
     */
    public function create($className, $data = array())
    {
        $method = $this->_objectManager->create($className, $data);
        if (!($method instanceof \Magento\Payment\Model\Method)) {
            throw new \Magento\Core\Exception(sprintf("%s class doesn't implement \Magento\Payment\Model\Method",
                $className));
        }
        return $method;
    }
}
