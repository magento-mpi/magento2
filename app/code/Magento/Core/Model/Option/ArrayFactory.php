<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Option;

/**
 * Array optioned object factory
 */
class ArrayFactory
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
     * Create array optioned object
     *
     * @param $model
     * @param array $data
     * @throws InvalidArgumentException
     * @return \Magento\Core\Model\Option\ArrayInterface
     */
    public function create($model, array $data = array())
    {
        $modelInstance = $this->_objectManager->create($model, $data);
        if (false == ($modelInstance instanceof \Magento\Core\Model\Option\ArrayInterface)) {
            throw new InvalidArgumentException(
                $model . 'doesn\'t implement \Magento\Core\Model\Option\ArrayInterface'
            );
        }
        return $modelInstance;
    }
}
