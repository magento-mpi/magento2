<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Test_ObjectManager extends Magento_ObjectManager_Zend
{
    /**
     * Clear InstanceManager cache
     *
     * @return Magento_Test_ObjectManager
     */
    public function clearCache()
    {
        $instanceManagerNew = new Magento_Di_InstanceManager_Zend();
        $instanceManagerNew->addSharedInstance($this, 'Magento_ObjectManager');
        if ($this->_di->instanceManager()->hasSharedInstance('Mage_Core_Model_Resource')) {
            $resource = $this->_di->instanceManager()->getSharedInstance('Mage_Core_Model_Resource');
            $instanceManagerNew->addSharedInstance($resource, 'Mage_Core_Model_Resource');
        }
        $this->_di->setInstanceManager($instanceManagerNew);

        return $this;
    }
}
