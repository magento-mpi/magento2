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
    Zend\Di\DefinitionList,
    Zend\Di\Configuration,
    Zend\Di\Definition,
    Zend\Di\InstanceManager;

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
     * @var Zend\Di\Di
     */
    protected $_di;

    /**
     * @param string $definitionsFile
     * @param Zend\Di\Di $diInstance
     */
    public function __construct($definitionsFile = null, Zend\Di\Di $diInstance = null)
    {
        Magento_Profiler::start('di');

        if (is_file($definitionsFile) && is_readable($definitionsFile)) {
            $definition = new Definition\ArrayDefinition(unserialize(file_get_contents($definitionsFile)));
        } else {
            $definition = new Definition\RuntimeDefinition();
        }

        $this->_di = $diInstance ? $diInstance : new Magento_Di();
        $this->_di->setDefinitionList(new DefinitionList($definition));

        $this->_initializeInstanceManager();

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
    public function loadAreaConfiguration($areaCode)
    {
        /** @var $magentoConfiguration Mage_Core_Model_Config */
        $magentoConfiguration = $this->get('Mage_Core_Model_Config');
        $node = $magentoConfiguration->getNode($areaCode . '/' . self::CONFIGURATION_DI_NODE);
        if ($node) {
            $diConfiguration = new Configuration(array('instance' => $node->asArray()));
            $diConfiguration->configure($this->_di);
        }
        return $this;
    }

    /**
     * Initialize instance manager content
     *
     * @return Magento_ObjectManager_Zend
     */
    protected function _initializeInstanceManager()
    {
        $this->_di->instanceManager()->addSharedInstance($this, 'Magento_ObjectManager');

        /** @var $magentoConfiguration Mage_Core_Model_Config */
        $magentoConfiguration = $this->get('Mage_Core_Model_Config');
        $magentoConfiguration->loadBase();
        $this->loadAreaConfiguration(self::CONFIGURATION_AREA);

        return $this;
    }
}
