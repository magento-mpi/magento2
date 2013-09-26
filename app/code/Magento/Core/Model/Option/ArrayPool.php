<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Array optioned object factory
 */
namespace Magento\Core\Model\Option;

class ArrayPool
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Get array optioned object
     *
     * @param string $model
     * @throws \InvalidArgumentException
     * @return \Magento\Core\Model\Option\ArrayInterface
     */
    public function get($model)
    {
        $modelInstance = $this->_objectManager->get($model);
        if (false == ($modelInstance instanceof \Magento\Core\Model\Option\ArrayInterface)) {
            throw new \InvalidArgumentException(
                $model . 'doesn\'t implement \Magento\Core\Model\Option\ArrayInterface'
            );
        }
        return $modelInstance;
    }
}
