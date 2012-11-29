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
     * Classes with xml properties to explicitly call __destruct() due to https://bugs.php.net/bug.php?id=62468
     *
     * @var array
     */
    protected $_classesToDestruct = array(
        'Mage_Core_Model_Config',
        'Mage_Core_Model_Layout',
        'Mage_Core_Model_Layout_Merge',
        'Mage_Core_Model_Layout_ScheduledStructure',
    );

    /**
     * @param string $definitionsFile
     * @param Zend\Di\Di $diInstance
     */
    public function __construct($definitionsFile = null, Zend\Di\Di $diInstance = null)
    {
        $diInstance = $diInstance ? $diInstance : new Magento_Di();
        $diInstance->setInstanceManager(new Magento_Test_Di_InstanceManager());
        parent::__construct($definitionsFile, $diInstance);
    }

    /**
     * Clear InstanceManager cache
     *
     * @return Magento_Test_ObjectManager
     */
    public function clearCache()
    {
        foreach ($this->_classesToDestruct as $className) {
            if ($this->_di->instanceManager()->hasSharedInstance($className)) {
                /** @var $object object */
                $object = $this->_di->instanceManager()->getSharedInstance($className);
                // force to cleanup circular references
                $object->__destruct();
            }
        }
        $instanceManagerNew = new Magento_Test_Di_InstanceManager();
        $instanceManagerNew->addSharedInstance($this, 'Magento_ObjectManager');
        if ($this->_di->instanceManager()->hasSharedInstance('Mage_Core_Model_Resource')) {
            $resource = $this->_di->instanceManager()->getSharedInstance('Mage_Core_Model_Resource');
            $instanceManagerNew->addSharedInstance($resource, 'Mage_Core_Model_Resource');
        }
        $this->_di->setInstanceManager($instanceManagerNew);
        return $this;
    }

    /**
     * Remove shared instance
     *
     * @param string $classOrAlias
     * @return Magento_Test_ObjectManager
     */
    public function removeSharedInstance($classOrAlias)
    {
        /** @var $instanceManager Magento_Test_Di_InstanceManager */
        $instanceManager = $this->_di->instanceManager();
        $instanceManager->removeSharedInstance($classOrAlias);

        return $this;
    }
}
