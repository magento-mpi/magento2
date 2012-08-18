<?php

use Zend\Di\Di,
    Zend\Di\DefinitionList,
    Zend\Di\Configuration,
    Zend\Di\Definition;

class Magento_ObjectManager_Zend extends Magento_ObjectManager_ObjectManagerAbstract
{
    /**
     * @var \Zend\Di\Di
     */
    protected $_di;

    /**
     * @param string $varDir
     */
    public function __construct($varDir)
    {
        $definition = null;
        if (file_exists($varDir . '/di/definitions.php')) {
            $definition = new Definition\ArrayDefinition(
                unserialize(file_get_contents($varDir . '/di/definitions.php'))
            );
        } else {
            $definition = new Definition\RuntimeDefinition();
        }

        $this->_di = new Di(new DefinitionList($definition));
        $this->_di->instanceManager()->addSharedInstance($this, "Magento_ObjectManager");
        $config = $this->get('Mage_Core_Model_Config');
        $config->loadBase();
        $config = new  Configuration(array('instance' => $config->getNode('global/di')->asArray()));
        $config->configure($this->_di);
    }

    /**
     * Create new object instance
     *
     * @param string $className
     * @param array $arguments
     * @return mixed
     */
    public function create($className, array $arguments = array())
    {
        $ni =  $this->_di->newInstance($className, $arguments);
        return $ni;
    }

    /**
     * Retreive cached object instance
     *
     * @param string $objectName
     * @param array $arguments
     * @return mixed
     */
    public function get($className, array $arguments = array())
    {
        $ni = $this->_di->get($className, $arguments);
        return $ni;
    }

    /**
     * @param string $areaCode
     */
    public function loadAreaConfiguration($areaCode)
    {
        $node = $this->_di->get('Mage_Core_Model_Config')->getNode($areaCode . '/di');
        if ($node) {
            $config = new Configuration(
                array('instance' => $node->asArray())
            );
            $config->configure($this->_di);
        }
    }

    public function reset()
    {
        //$this->_di->instanceManager()->unsetTypePreferences();
    }
}
