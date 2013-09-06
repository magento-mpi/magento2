<?php
/**
 * Test object manager
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_ObjectManager extends Magento_Core_Model_ObjectManager
{
    /**
     * Classes with xml properties to explicitly call __destruct() due to https://bugs.php.net/bug.php?id=62468
     *
     * @var array
     */
    protected $_classesToDestruct = array(
        'Magento_Core_Model_Layout',
    );

    /**
     * Clear InstanceManager cache
     *
     * @return Magento_Test_ObjectManager
     */
    public function clearCache()
    {
        foreach ($this->_classesToDestruct as $className) {
            if (isset($this->_sharedInstances[$className])) {
                $this->_sharedInstances[$className]->__destruct();
            }
        }

        Magento_Core_Model_Config_Base::destroy();
        $sharedInstances = array('Magento_ObjectManager' => $this, 'Magento_Core_Model_ObjectManager' => $this);
        if (isset($this->_sharedInstances['Magento_Core_Model_Resource'])) {
            $sharedInstances['Magento_Core_Model_Resource'] = $this->_sharedInstances['Magento_Core_Model_Resource'];
        }
        $this->_sharedInstances = $sharedInstances;
        $this->_config->clean();

        return $this;
    }

    /**
     * Add shared instance
     *
     * @param mixed $instance
     * @param string $className
     */
    public function addSharedInstance($instance, $className)
    {
        $this->_sharedInstances[$className] = $instance;
    }

    /**
     * Remove shared instance
     *
     * @param string $className
     */
    public function removeSharedInstance($className)
    {
        unset($this->_sharedInstances[$className]);
    }

    /**
     * Load primary DI configuration
     *
     * @param array $configData
     */
    public function loadPrimaryConfig($configData)
    {
        if ($configData) {
            $this->configure($configData);
        }
    }

   /**
    * Set objectManager
    *
    * @param Magento_ObjectManager $objectManager
    * @return Magento_ObjectManager
    */
    public static function setInstance(Magento_ObjectManager $objectManager)
    {
        return self::$_instance = $objectManager;
    }
}
