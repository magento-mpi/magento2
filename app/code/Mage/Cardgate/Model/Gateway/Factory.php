<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Cardgate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gateway factory
 */
class Mage_Cardgate_Model_Gateway_Factory
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
     * @param $modelName
     * @throws InvalidArgumentException
     * @return Mage_Cardgate_Model_Gateway_Abstract
     */
    public function create($modelName)
    {
        $modelName = 'Mage_Cardgate_Model_Gateway_' . ucfirst($modelName);
        /** @var Mage_Cardgate_Model_Gateway_Abstract $model */
        $model = $this->_objectManager->create($modelName);
        if (!($model instanceof Mage_Cardgate_Model_Gateway_Abstract)) {
            throw new InvalidArgumentException(
                'Invalid Model Name: ' . $modelName . ' is not instance of Mage_Cardgate_Model_Gateway_Abstract.'
            );
        }
        return $model;
    }
}
