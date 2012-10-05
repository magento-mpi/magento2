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
Zend\Di\Definition;

/**
 * General implementation of Magento_ObjectManager based on Zend DI
 */
class Magento_ObjectManager_Zend implements Magento_ObjectManager
{
    /**
     * Dependency injection config node name
     */
    const CONFIG_DI_NODE = 'di';

    /**
     * Zend dependency injection instance
     *
     * @var \Zend\Di\Di
     */
    protected $_di;

    /**
     * @param string $definitionsFile
     * @param Magento_Di $magentoDi
     */
    public function __construct($definitionsFile = null, Magento_Di $magentoDi = null)
    {
        Magento_Profiler::start('di');

        $definition = null;
        if ($definitionsFile && file_exists($definitionsFile)) {
            $definition = new Definition\ArrayDefinition(unserialize(file_get_contents($definitionsFile)));
        } else {
            $definition = new Definition\RuntimeDefinition();
        }

        $definitionsList = new DefinitionList($definition);
        if ($magentoDi) {
            $this->_di = $magentoDi;
            $this->_di->setDefinitionList($definitionsList);
        } else {
            $this->_di = new Magento_Di($definitionsList);
        }
        $this->_di->instanceManager()->addSharedInstance($this, 'Magento_ObjectManager');

        /** @var $magentoConfiguration Mage_Core_Model_Config */
        $magentoConfiguration = $this->_di->get('Mage_Core_Model_Config');
        $magentoConfiguration->loadBase();

        $this->loadAreaConfiguration(Mage_Core_Model_App_Area::AREA_GLOBAL);

        Magento_Profiler::stop('di');
    }

    /**
     * Create new object instance
     *
     * @param string $className
     * @param array $arguments
     * @return object
     */
    public function create($className, array $arguments = array())
    {
        $object = $this->_di->newInstance($className, $arguments);
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
        $magentoConfiguration = $this->_di->get('Mage_Core_Model_Config');
        $node = $magentoConfiguration->getNode($areaCode . '/' . self::CONFIG_DI_NODE);
        if ($node) {
            $diConfiguration = new Configuration(array('instance' => $node->asArray()));
            $diConfiguration->configure($this->_di);
        }
        return $this;
    }
}
