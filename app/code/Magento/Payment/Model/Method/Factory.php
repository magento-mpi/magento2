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
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Construct
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Creates new instances of payment method models
     *
     * @param string $className
     * @param array $data
     * @return \Magento\Payment\Model\MethodInterface
     * @throws \Magento\Framework\Model\Exception
     */
    public function create($className, $data = array())
    {
        $method = $this->_objectManager->create($className, $data);
        if (!$method instanceof \Magento\Payment\Model\MethodInterface) {
            throw new \Magento\Framework\Model\Exception(
                sprintf("%s class doesn't implement \Magento\Payment\Model\MethodInterface", $className)
            );
        }
        return $method;
    }
}
