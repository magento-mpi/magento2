<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme factory
 */
class Mage_Core_Model_Theme_Factory
{
    /**
     * Object Manager
     *
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
     * Create new instance
     *
     * @param array $data
     * @return Mage_Core_Model_Theme
     */
    public function create(array $data = array())
    {
        return $this->_objectManager->create('Mage_Core_Model_Theme', $data);
    }
}
