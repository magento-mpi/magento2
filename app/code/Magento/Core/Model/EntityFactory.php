<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_EntityFactory
{
    /**
     * Object Manager instance
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager = null;

    /**
     * Factory constructor
     *
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param $className
     * @param array $data
     * @throws LogicException
     * @return Magento_Object
     */
    public function create($className, array $data = array())
    {
        $model = $this->_objectManager->create($className, $data);
        //TODO: fix that when this factory used only for Magento_Core_Model_Abstract
        //if (!$model instanceof Magento_Core_Model_Abstract) {
        //    throw new LogicException($className . ' doesn\'t implement Magento_Core_Model_Abstract');
        //}
        return $model;
    }
}
