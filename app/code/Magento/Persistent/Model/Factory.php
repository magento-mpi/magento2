<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Persistent Factory
 */
class Magento_Persistent_Model_Factory
{
    /**
     * Object manager
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
     * Creates models
     *
     * @param string $className
     * @param array $data
     * @return mixed
     */
    public function create($className, $data = array())
    {
        return $this->_objectManager->create($className, $data);
    }
}
