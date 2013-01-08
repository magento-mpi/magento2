<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ObjectManager
 * @copyright   {copyright}
 * @license     {license_link}
 */

use Zend\Di\Di,
    Zend\Di\Config,
    Zend\Di\Definition;

/**
 * General implementation of Magento_ObjectManager based on Zend DI
 */
class Magento_ObjectManager_Zend implements Magento_ObjectManager
{
    /**
     * Default configuration area name
     */
    const CONFIGURATION_AREA = 'global';

    /**
     * Dependency injection configuration node name
     */
    const CONFIGURATION_DI_NODE = 'di';

    /**
     * Dependency injection instance
     *
     * @var Magento_Di_Zend
     */
    protected $_di;

    /**
     * @param string $definitionsFile
     * @param Magento_Di $diInstance
     * @param Magento_Di_InstanceManager $instanceManager
     */
    public function __construct(
        $definitionsFile = null,
        Magento_Di $diInstance = null,
        Magento_Di_InstanceManager $instanceManager = null
    ) {
        Magento_Profiler::start('di');

        $this->_di = $diInstance ?: new Magento_Di_Zend(null, $instanceManager, null, $definitionsFile);
        $this->_di->instanceManager()->addSharedInstance($this, 'Magento_ObjectManager');

        Magento_Profiler::stop('di');
    }

    /**
     * Create new object instance
     *
     * @param string $className
     * @param array $arguments
     * @param bool $isShared
     * @return object
     */
    public function create($className, array $arguments = array(), $isShared = true)
    {
        $object = $this->_di->newInstance($className, $arguments, $isShared);

        return $object;
    }

    /**
     * Retrieve cached object instance
     *
     * @param string $className
     * @param array $arguments
     * @return object
     */
    public function get($className, array $arguments = array())
    {
        $object = $this->_di->get($className, $arguments);

        return $object;
    }

    /**
     * Load DI configuration for specified config area
     *
     * @param string $areaCode
     * @return Magento_ObjectManager_Zend
     */
    public function loadAreaConfiguration($areaCode = null)
    {
        if (!$areaCode) {
            $areaCode = self::CONFIGURATION_AREA;
        }

        /** @var $magentoConfiguration Mage_Core_Model_Config */
        $magentoConfiguration = $this->get('Mage_Core_Model_Config');
        $node                 = $magentoConfiguration->getNode($areaCode . '/' . self::CONFIGURATION_DI_NODE);
        if ($node) {
            $diConfiguration = new Config(array('instance' => $node->asArray()));
            $diConfiguration->configure($this->_di);
        }

        return $this;
    }

    /**
     * A proxy for adding shared instance
     *
     * Normally Di object manager determines a hash based on the class name and incoming arguments.
     * But in client code it is inconvenient (or nearly impossible) to "know" arguments for the objects you depend on.
     * This is a dirty hack that allows bypassing "hash checking" by Di object manager and therefore referring
     * to an instance using class name (or alias), but not specifying its non-injectable arguments.
     *
     * @param object $instance
     * @param string $classOrAlias
     * @return Magento_ObjectManager_Zend
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
     * @return Magento_ObjectManager_Zend
     */
    public function removeSharedInstance($classOrAlias)
    {
        /** @var $instanceManager Magento_Di_InstanceManager_Zend */
        $instanceManager = $this->_di->instanceManager();
        $instanceManager->removeSharedInstance($classOrAlias);

        return $this;
    }

    /**
     * Check whether instance manager has shared instance of given class (alias)
     *
     * @param string $classOrAlias
     * @return bool
     */
    public function hasSharedInstance($classOrAlias)
    {
        /** @var $instanceManager Magento_Di_InstanceManager_Zend */
        $instanceManager = $this->_di->instanceManager();
        return $instanceManager->hasSharedInstance($classOrAlias);
    }

    /**
     * Add alias
     *
     * @param  string $alias
     * @param  string $class
     * @param  array  $parameters
     * @return Magento_ObjectManager_Zend
     * @throws Zend\Di\Exception\InvalidArgumentException
     */
    public function addAlias($alias, $class, array $parameters = array())
    {
        $this->_di->instanceManager()->addAlias($alias, $class, $parameters);

        return $this;
    }

    /**
     * Get class name by alias
     *
     * @param string
     * @return string|bool
     * @throws Zend\Di\Exception\RuntimeException
     */
    public function getClassFromAlias($alias)
    {
        return $this->_di->instanceManager()->getClassFromAlias($alias);
    }
}
