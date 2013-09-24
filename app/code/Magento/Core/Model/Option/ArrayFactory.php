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
class Magento_Core_Model_Option_ArrayFactory
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
     * Create array optioned object
     *
     * @param $model
     * @param array $data
     * @throws InvalidArgumentException
     * @return Magento_Core_Model_Option_ArrayInterface
     */
    public function create($model, array $data = array())
    {
        $modelInstance = $this->_objectManager->create($model, $data);
        if (false == ($modelInstance instanceof Magento_Core_Model_Option_ArrayInterface)) {
            throw new InvalidArgumentException(
                $model . 'doesn\'t implement Magento_Core_Model_Option_ArrayInterface'
            );
        }
        return $modelInstance;
    }
}
