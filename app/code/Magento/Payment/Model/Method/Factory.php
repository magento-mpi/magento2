<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class \Magento\Payment\Model\Method\Factory
 */
namespace Magento\Payment\Model\Method;

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
     * @return \Magento\Payment\Model\Method\AbstractMethod
     * @throws \Magento\Core\Exception
     */
    public function create($className, $data = array())
    {
        $method = $this->_objectManager->create($className, $data);
        if (!($method instanceof \Magento\Payment\Model\Method\AbstractMethod)) {
            throw new \Magento\Core\Exception(sprintf("%s class doesn't extend \Magento\Payment\Model\Method\AbstractMethod",
                $className));
        }
        return $method;
    }
}
