<?php
/**
 * Test object manager
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_ObjectManager extends Mage_Core_Model_ObjectManager
{
    /**
     * Classes with xml properties to explicitly call __destruct() due to https://bugs.php.net/bug.php?id=62468
     *
     * @var array
     */
    protected $_classesToDestruct = array(
        'Mage_Core_Model_Layout',
    );

    /**
     * Clear InstanceManager cache
     *
     * @return Magento_Test_ObjectManager
     */
    public function clearCache()
    {
        foreach ($this->_classesToDestruct as $className) {
            if ($this->_di->instanceManager()->hasSharedInstance($className)) {
                $object = $this->_di->instanceManager()->getSharedInstance($className);
                if ($object) {
                    // force to cleanup circular references
                    $object->__destruct();
                }
            }
        }

        Mage::getSingleton('Mage_Core_Model_Config_Base')->destroy();
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
