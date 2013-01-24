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
 * Store Group factory
 */
class Mage_Core_Model_Store_Group_Factory implements Magento_ObjectManager_Factory
{
    /**
     * Store group model class name
     */
    const CLASS_NAME = 'Mage_Core_Model_Store_Group';

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
     * @param array $arguments
     * @return mixed
     */
    public function createFromArray(array $arguments = array())
    {
        return $this->_objectManager->get(self::CLASS_NAME, $arguments);
    }
}
