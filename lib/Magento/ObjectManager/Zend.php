<?php

use Zend\Di\Di;

class Magento_ObjectManager_Zend extends Magento_ObjectManager_ObjectManagerAbstract
{
    /**
     * @var \Zend\Di\Di
     */
    protected $_di;

    public function __construct(\Zend\Di\Di $di, Mage_Core_Model_Config $config)
    {
        $this->_di = $di;
        $definitions = $this->_di->definitions();
        $definitions[0]->getIntrospectionStrategy()->setMethodNameInclusionPatterns(array());
        $definitions[0]->getIntrospectionStrategy()->setInterfaceInjectionInclusionPatterns(array());
        $this->_di->instanceManager()->setParameters(
            'Mage_Core_Model_Cache',
            array(
                'cacheOptions' => $config->getNode('global/cache')->asArray(),
                'Mage_Core_Model_Config' => $config
            )
        );
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
        return $this->_di->newInstance($className, $arguments);
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
        return $this->_di->get($className, $arguments);
    }

}
