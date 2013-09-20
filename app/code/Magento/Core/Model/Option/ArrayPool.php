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
     * @var array
     */
    protected $_pool = array();

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
        if (!empty($this->_pool[$model])) {
            return $this->_pool[$model];
        }
        $modelInstance = $this->_objectManager->create($model);
        if (false == ($modelInstance instanceof Magento_Core_Model_Option_ArrayInterface)) {
            throw new InvalidArgumentException(
                $model . 'doesn\'t implement Magento_Core_Model_Option_ArrayInterface'
            );
        }
        $this->_pool[$model] = $modelInstance;
        return $modelInstance;
    }
}
