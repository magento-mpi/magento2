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

use Zend\Di\InstanceManager;

class Magento_Test_ObjectManager extends Magento_ObjectManager_Zend
{
    /**
     * Clear InstanceManager cache
     *
     * @return Magento_Test_ObjectManager
     */
    public function clearCache()
    {
        $resource = $this->get('Mage_Core_Model_Resource');
        $this->_di->setInstanceManager(new InstanceManager());
        $this->_initializeInstanceManager();
        if ($resource) {
            $this->addSharedInstance($resource, 'Mage_Core_Model_Resource');
        }

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
}
