<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Option;

/**
 * Array optioned object factory
 */
class ArrayPool
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Get array optioned object
     *
     * @param string $model
     * @throws \InvalidArgumentException
     * @return \Magento\Option\ArrayInterface
     */
    public function get($model)
    {
        $modelInstance = $this->_objectManager->get($model);
        if (false == $modelInstance instanceof \Magento\Option\ArrayInterface) {
            throw new \InvalidArgumentException($model . 'doesn\'t implement \Magento\Option\ArrayInterface');
        }
        return $modelInstance;
    }
}
