<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Model factory
 */
class Magento_Catalog_Model_Factory
{
    /**
     * Object Manager
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Construct
     *
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create model
     *
     * @param string $className
     * @param array $data
     * @return Magento_Core_Model_Abstract
     * @throws Magento_Core_Exception
     */
    public function create($className, array $data = array())
    {
        $model = $this->_objectManager->create($className, $data);

        if (!$model instanceof Magento_Core_Model_Abstract) {
            throw new Magento_Core_Exception($className
                . ' doesn\'t extends Magento_Core_Model_Abstract');
        }
        return $model;
    }
}
