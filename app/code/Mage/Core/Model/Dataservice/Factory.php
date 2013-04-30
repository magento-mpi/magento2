<?php
/**
 * Dataservice factory
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Dataservice_Factory implements Mage_Core_Model_Dataservice_Path_Visitable
{
    /**
     * @var Mage_Core_Model_Dataservice_Config_Interface
     */
    protected $_config;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /** @var Mage_Core_Model_Dataservice_Repository */
    protected $_repository;

    /** @var Mage_Core_Model_Dataservice_Path_Composite */
    protected $_composite;

    /**
     * @param Mage_Core_Model_Dataservice_Config_Interface $config
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Model_Dataservice_Repository $repository
     */
    public function __construct(
        Mage_Core_Model_Dataservice_Config_Interface $config,
        Magento_ObjectManager $objectManager,
        //Mage_Core_Model_Dataservice_Path_Composite $composite,
        Mage_Core_Model_Dataservice_Repository $repository
    ) {
        $this->_config = $config;
        $this->_objectManager = $objectManager;
        // $this->_composite = $composite;
        $this->_repository = $repository;
    }

    /**
     * @return Mage_Core_Model_Dataservice_Config_Interface
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * takes array of the following structure
     * and initializes all of the data sources
     *
     *  array(dataServiceName => array(
     *      blocks => array(
     *          'namespace' => aliasInNamespace
     *      ))
     *
     * @param array $dataServicesList
     * @return Mage_Core_Model_Dataservice_Factory
     */
    public function init(array $dataServicesList)
    {
        foreach ($dataServicesList as $dataServiceName => $namespaceConfig) {
            $this->initDataService($dataServiceName);
            $this->assignToNamespace($dataServiceName, $namespaceConfig);
        }
        return $this;
    }

    /**
     * Assign service call name to the namespace
     * @param $dataServiceName
     * @param $namespaceConfig
     * @throws Exception
     */
    public function assignToNamespace($dataServiceName, $namespaceConfig)
    {
        if (!isset($namespaceConfig['namespaces'])) {
            throw new Exception("Data reference configuration doesn't have a block to link to");
        }
        foreach ($namespaceConfig['namespaces'] as $namespaceName => $aliasInNamespace) {
            $this->getRepository()->addNameInNamespace($namespaceName, $dataServiceName, $aliasInNamespace);
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
        $dataServices =  $this->getRepository()->getByNamespace($namespace);
        return $dataServices;
    }

    /**
     * Retrieve the data or the service call based on its name
     * @param $sourceName
     * @return bool|mixed
     */
    public function get($sourceName)
    {
        $dataService = $this->getRepository()->get($sourceName);
        if ($dataService == null) {
            $dataService = $this->initDataService($sourceName);
        }
        return $dataService;
    }

    /**
     * Retrieve repository for the data from service calls
     * @return array|Mage_Core_Model_Dataservice_Repository
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
    public function initDataService($sourceName)
    {
        if ($dataService = $this->getRepository()->get($sourceName) !== null) {
            return $dataService;
        }

        $classInformation = $this->getConfig()->getClassByAlias($sourceName);
        $instance = $this->_objectManager->create($classInformation['class']);
        $dataService = $this->_applyMethod($instance, $classInformation['retrieveMethod'],
            $classInformation['methodArguments']);

        $this->getRepository()->add($sourceName, $dataService);
        return $dataService;
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

        /** @var $visitor Mage_Core_Model_Dataservice_Path_Visitor */
        $visitor = $this->_objectManager->create('Mage_Core_Model_Dataservice_Path_Visitor',
            array('path' => $path, 'separator' => '.'));
        /** @var $pathRepository Mage_Core_Model_Dataservice_Path_Composite */
        $pathRepository = $this->_objectManager->create('Mage_Core_Model_Dataservice_Path_Composite');
        $result = $visitor->visit($pathRepository);

        /** @var $visitor Mage_Core_Model_Dataservice_Path_Visitor */
        //$visitor = $this->_objectManager->create('Mage_Core_Model_Dataservice_Path_Visitor',
        //    array('path' => $path, 'separator' => '.'));
        //$result = $visitor->visit($this->_composite);
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
     * Make the Dataservice Object visitable
     *
     * @param Mage_Core_Model_Dataservice_Path_Visitor $visitor
     * @return bool|mixed
     */
    public function visit(Mage_Core_Model_Dataservice_Path_Visitor $visitor)
    {
        // return $this->getRepository()->visit($visitor);
        return $this->get($visitor->getCurrentPathElement());
    }
}
