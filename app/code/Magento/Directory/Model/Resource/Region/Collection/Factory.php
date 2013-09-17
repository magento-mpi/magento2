<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Factory for region resource collections
 */
class Magento_Directory_Model_Resource_Region_Collection_Factory
{
    /**
     * Object Manager
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * @param array $data
     * @return Magento_Directory_Model_Resource_Region_Collection
     */
    public function create(array $data = array())
    {
        return $this->_objectManager->create('Magento_Directory_Model_Resource_Region_Collection', $data);
    }
}
