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
 * Website factory
 */
class Mage_Core_Model_Website_Factory
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
     * @return Mage_Core_Model_Website
     */
    public function create(array $data = array())
    {
        return $this->_objectManager->create('Mage_Core_Model_Website', $data);
    }
}
