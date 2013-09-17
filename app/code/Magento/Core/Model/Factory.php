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
 * Model object factory
 */
class Magento_Core_Model_Factory
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
     * Create new model object
     *
     * @param $model
     * @param array $data
     * @throws InvalidArgumentException
     * @return Magento_Core_Model_Abstract
     */
    public function create($model, array $data = array())
    {
        $modelInstance = $this->_objectManager->create($model, $data);
        if (false == ($modelInstance instanceof Magento_Core_Model_Abstract)) {
            throw new InvalidArgumentException(
                $model . ' is not instance of Magento_Core_Model_Abstract'
            );
        }
        return $modelInstance;
    }
}
