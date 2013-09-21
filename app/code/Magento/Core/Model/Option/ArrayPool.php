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
class Magento_Core_Model_Option_ArrayPool
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Get array optioned object
     *
     * @param $model
     * @throws InvalidArgumentException
     * @return Magento_Core_Model_Option_ArrayInterface
     */
    public function get($model)
    {
        $modelInstance = $this->_objectManager->get($model);
        if (false == ($modelInstance instanceof Magento_Core_Model_Option_ArrayInterface)) {
            throw new InvalidArgumentException(
                $model . 'doesn\'t implement Magento_Core_Model_Option_ArrayInterface'
            );
        }
        return $modelInstance;
    }
}
