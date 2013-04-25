<?php
/**
 * Datasource factory
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Datasource_Factory implements Mage_Core_Model_Datasource_Path_Visitable
{
    /**
     * @var Mage_Core_Model_Datasource_Config_Interface
     */
    protected $_config;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /** @var Mage_Core_Model_Datasource_Repository */
    protected $_repository;

    /**
     * @param Mage_Core_Model_Datasource_Config_Interface $config
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Model_Datasource_Repository $repository
     */
    public function __construct(
        Mage_Core_Model_Datasource_Config_Interface $config,
        Magento_ObjectManager $objectManager,
        Mage_Core_Model_Datasource_Repository $repository
    ) {
        $this->_config = $config;
        $this->_objectManager = $objectManager;
        $this->_repository = $repository;
    }

    /**
     * @return Mage_Core_Model_Datasource_Config_Interface
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * takes array of the following structure
     * and initializes all of the data sources
     *
     *  array(dataSourceName => array(
     *      blocks => array(
     *          'namespace' => aliasInNamespace
     *      ))
     *
     * @param array $dataSourcesList
     * @return Mage_Core_Model_Datasource_Factory
     */
    public function init(array $dataSourcesList)
    {
        foreach ($dataSourcesList as $dataSourceName => $namespaceConfig) {
            $this->initDataSource($dataSourceName);
            $this->assignToNamespace($dataSourceName, $namespaceConfig);
        }
        return $this;
    }

    /**
     * Assign service call name to the namespace
     * @param $dataSourceName
     * @param $namespaceConfig
     * @throws Exception
     */
    public function assignToNamespace($dataSourceName, $namespaceConfig)
    {
        if (!isset($namespaceConfig['namespaces'])) {
            throw new Exception("Data reference configuration doesn't have a block to link to");
        }
        foreach ($namespaceConfig['namespaces'] as $namespaceName => $aliasInNamespace) {
            $this->getRepository()->addNameInNamespace($namespaceName, $dataSourceName, $aliasInNamespace);
        }
    }

    /**
     * Retrieve all data for the service calls for particular namespace
     *
     * @param $namespace
     * @return mixed
     */
    public function getByNamespace($namespace)
    {
        $dataSources =  $this->getRepository()->getByNamespace($namespace);
        return $dataSources;
    }

    /**
     * Retrieve the data or the service call based on its name
     * @param $sourceName
     * @return bool|mixed
     */
    public function get($sourceName)
    {
        $dataSource = $this->getRepository()->get($sourceName);
        if ($dataSource == null) {
            $dataSource = $this->initDataSource($sourceName);
        }
        return $dataSource;
    }

    /**
     * Retrieve repository for the data from service calls
     * @return array|Mage_Core_Model_Datasource_Repository
     */
    public function getRepository()
    {
        return $this->_repository;
    }

    /**
     * Init single service call
     *
     * @param $sourceName
     * @return bool|mixed
     */
    public function initDataSource($sourceName)
    {
        if ($dataSource = $this->getRepository()->get($sourceName) !== null) {
            return $dataSource;
        }

        $classInformation = $this->getConfig()->getClassByAlias($sourceName);
        $instance = $this->_objectManager->create($classInformation['class']);
        $dataSource = $this->_applyMethod($instance, $classInformation['retrieveMethod'],
            $classInformation['methodArguments']);

        $this->getRepository()->add($sourceName, $dataSource);
        return $dataSource;
    }

    /**
     * Invoke method configuraed for service call
     *
     * @param $object
     * @param $methodName
     * @param $methodArguments
     * @return mixed
     */
    protected function _applyMethod($object, $methodName, $methodArguments)
    {
        $result = null;
        $arguments = array();
        if (is_array($methodArguments)) {
            $arguments = $this->_prepareArguments($methodArguments);
        }
        $result = call_user_func_array(array($object, $methodName), $arguments);
        return $result;
    }

    /**
     * Get the value for the method argument
     *
     * @param $path
     * @return null
     */
    public function getArgumentValue($path)
    {
        /** @var $visitor Mage_Core_Model_Datasource_Path_Visitor */
        $visitor = $this->_objectManager->create('Mage_Core_Model_Datasource_Path_Visitor',
            array('path' => $path, 'separator' => '.'));
        /** @var $pathRepository Mage_Core_Model_Datasource_Path_Composite */
        $pathRepository = $this->_objectManager->create('Mage_Core_Model_Datasource_Path_Composite');
        $result = $visitor->visit($pathRepository);
        return $result;
    }

    /**
     * Prepare  values for the method params
     *
     * @param $argumentsList
     * @return array
     */
    protected function _prepareArguments($argumentsList)
    {
        $result = array();
        foreach ($argumentsList as $name => $value) {
            $result[$name] = $this->getArgumentValue($value);
        }
        return $result;
    }


    /**
     * Make the Datasource Object visitable
     *
     * @param Mage_Core_Model_Datasource_Path_Visitor $visitor
     * @return bool|mixed
     */
    public function visit(Mage_Core_Model_Datasource_Path_Visitor $visitor)
    {
        return $this->get($visitor->getCurrentPathElement());
    }
}
