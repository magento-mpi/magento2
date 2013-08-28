<?php
/**
 * DataService graph manages creation and storage of data services.
 *
 * manages the graph of objects
 *  - initializes data service
 *  - calls factory to retrieve data
 *  - stores data to repository
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_DataService_Graph implements Magento_Core_Model_DataService_Path_NodeInterface
{
    /** @var Magento_Core_Model_DataService_Invoker */
    protected $_invoker;

    /** @var Magento_Core_Model_DataService_Repository */
    protected $_repository;

    /**
     * @param Magento_Core_Model_DataService_Invoker $dataServiceInvoker
     * @param Magento_Core_Model_DataService_Repository $repository
     */
    public function __construct(
        Magento_Core_Model_DataService_Invoker $dataServiceInvoker,
        Magento_Core_Model_DataService_Repository $repository
    ) {
        $this->_invoker = $dataServiceInvoker;
        $this->_repository = $repository;
    }

    /**
     * Get the value for the method argument
     *
     * @param string $path
     * @return mixed
     */
    public function getArgumentValue($path)
    {
        return $this->_invoker->getArgumentValue($path);
    }

    /**
     * Takes array of the following structure
     * and initializes all of the data sources
     *
     *  array(dataServiceName => array(
     *      blocks => array(
     *          'namespace' => aliasInNamespace
     *      ))
     *
     * @param array $dataServicesList
     * @return Magento_Core_Model_DataService_Graph
     * @throws InvalidArgumentException
     */
    public function init(array $dataServicesList)
    {
        foreach ($dataServicesList as $dataServiceName => $namespaceConfig) {
            if (!isset($namespaceConfig['namespaces'])) {
                throw new InvalidArgumentException("Data reference configuration doesn't have a block to link to");
            }
            if ($this->get($dataServiceName) === false) {
                throw new InvalidArgumentException("Data service '$dataServiceName' couldn't be retrieved");
            }
            foreach ($namespaceConfig['namespaces'] as $namespaceName => $aliasInNamespace) {
                $this->_repository->setAlias($namespaceName, $dataServiceName, $aliasInNamespace);
            }
        }
        return $this;
    }

    /**
     * Retrieve the data or the service call based on its name
     *
     * @param string $dataServiceName
     * @return bool|array
     */
    public function get($dataServiceName)
    {
        $dataService = $this->_repository->get($dataServiceName);
        if ($dataService === null) {
            $dataService = $this->_invoker->getServiceData($dataServiceName);
            $this->_repository->add($dataServiceName, $dataService);
        }
        return $dataService;
    }

    /**
     * Retrieve all data for the service calls for particular namespace.
     *
     * @param string $namespace
     * @return mixed
     */
    public function getByNamespace($namespace)
    {
        return $this->_repository->getByNamespace($namespace);
    }

    /**
     * Return a child path node that corresponds to the input path element.  This can be used to walk the
     * data service graph.  Leaf nodes in the graph tend to be of mixed type (scalar, array, or object).
     *
     * @param string $pathElement the path element name of the child node
     * @return Magento_Core_Model_DataService_Path_NodeInterface|mixed|null the child node,
     *    or mixed if this is a leaf node
     */
    public function getChildNode($pathElement)
    {
        return $this->get($pathElement);
    }
}
