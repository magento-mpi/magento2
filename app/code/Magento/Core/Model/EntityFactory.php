<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model;

class EntityFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager = null;

    /**
     * Factory constructor
     *
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param $className
     * @param array $data
     * @throws LogicException
     * @return \Magento\Object
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
