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
class Mage_Core_Model_DataService_Graph implements Mage_Core_Model_DataService_Path_NodeInterface
{
    /** @var Mage_Core_Model_DataService_Invoker */
    protected $_invoker;

    /** @var Mage_Core_Model_DataService_Repository */
    protected $_repository;

    /**
     * @param Mage_Core_Model_DataService_Invoker $dataServiceInvoker
     * @param Mage_Core_Model_DataService_Repository $repository
     */
    public function __construct(
        Mage_Core_Model_DataService_Invoker $dataServiceInvoker,
        Mage_Core_Model_DataService_Repository $repository
    ) {
        $this->_invoker = $dataServiceInvoker;
        $this->_repository = $repository;
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
     * @return Mage_Core_Model_DataService_Graph
     * @throws Mage_Core_Exception
     */
    public function init(array $dataServicesList)
    {
        foreach ($dataServicesList as $dataServiceName => $namespaceConfig) {
            if (!isset($namespaceConfig['namespaces'])) {
                throw new Mage_Core_Exception("Data reference configuration doesn't have a block to link to");
            }
            $this->get($dataServiceName);
            foreach ($namespaceConfig['namespaces'] as $namespaceName => $aliasInNamespace) {
                $this->_repository->addNameInNamespace($namespaceName, $dataServiceName, $aliasInNamespace);
            }
        }
        return $this;
    }

    /**
     * Retrieve the data or the service call based on its name
     *
     * @param string $dataServiceName
     * @return Mage_Core_Model_DataService_Path_NodeInterface|bool|mixed
     */
    public function get($dataServiceName)
    {
        $dataService = $this->_repository->get($dataServiceName);
        if ($dataService == null) {
            $dataService = $this->_invoker->getServiceData($dataServiceName);
        }
        $this->getRepository()->add($dataServiceName, $dataService);
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
        return $this->getRepository()->getByNamespace($namespace);
    }

    /**
     * Get repository object.
     *
     * @return \Mage_Core_Model_DataService_Repository
     */
    protected function getRepository()
    {
        return $this->_repository;
    }

    /**
     * Return a child path node that corresponds to the input path element.  This can be used to walk the
     * data service graph.  Leaf nodes in the graph tend to be of mixed type (scalar, array, or object).
     *
     * @param string $pathElement the path element name of the child node
     * @return Mage_Core_Model_DataService_Path_NodeInterface|mixed|null the child node, or mixed if this is a leaf node
     */
    public function getChildNode($pathElement)
    {
        return $this->get($pathElement);
    }
}
