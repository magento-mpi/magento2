<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cardgate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gateway factory
 */
class Magento_Cardgate_Model_Gateway_Factory
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
     * Create gateway object
     *
     * @param string $modelName
     * @throws InvalidArgumentException
     * @return Magento_Cardgate_Model_Gateway_Abstract
     */
    public function create($modelName)
    {
        $modelName = 'Magento_Cardgate_Model_Gateway_' . ucfirst($modelName);
        /** @var Magento_Cardgate_Model_Gateway_Abstract $model */
        $model = $this->_objectManager->create($modelName);
        if (!($model instanceof Magento_Cardgate_Model_Gateway_Abstract)) {
            throw new InvalidArgumentException(
                'Invalid Model Name: ' . $modelName . ' is not instance of Magento_Cardgate_Model_Gateway_Abstract.'
            );
        }
        return $model;
    }
}
