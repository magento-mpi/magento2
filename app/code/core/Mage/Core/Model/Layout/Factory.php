<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Layout_Factory implements Magento_ObjectManager_Factory
{
    const CLASS_NAME = 'Mage_Core_Model_Layout';

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
     * @param array $arguments
     * @return Mage_Core_Model_Layout
     */
    public function createFromArray(array $arguments = array())
    {
        return $this->_objectManager->get(static::CLASS_NAME, $arguments);
    }
}
