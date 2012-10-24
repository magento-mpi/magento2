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
     * @param string $definitionsFile
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
        $resource = $this->get('Mage_Core_Model_Resource');
        $this->_di->setInstanceManager(new Magento_Test_Di_InstanceManager());
        $this->addSharedInstance($this, 'Magento_ObjectManager');
        $this->addSharedInstance($resource, 'Mage_Core_Model_Resource');

        return $this;
    }

    /**
     * Add shared instance
     *
     * @param object $instance
     * @param string $classOrAlias
     * @return Magento_Test_ObjectManager
     * @throws Zend\Di\Exception\InvalidArgumentException
     */
    public function addSharedInstance($instance, $classOrAlias)
    {
        $this->_di->instanceManager()->addSharedInstance($instance, $classOrAlias);

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
