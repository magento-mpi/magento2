<?php
/**
 * This class reads config.xml of modules, and provides interface to the configuration of service calls.
 *
 * Service calls are defined in service_calls.xml files in etc directory of the modules.
 * Additionally, reference to service_calls.xml file is configured in config.xml file.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_DataService_Config implements Magento_Core_Model_DataService_ConfigInterface
{
    /** Xpath to service call */
    const SERVICE_CALLS_XPATH = '/service_calls/service_call';

    /** @var Magento_Core_Model_DataService_Config_Reader_Factory */
    protected $_readerFactory;

    /** @var  Magento_Core_Model_DataService_Config_Reader */
    protected $_reader;

    /** @var array $_serviceCallNodes */
    protected $_serviceCallNodes;

    /** @var Magento_Core_Model_Config_Modules_Reader  */
    protected $_moduleReader;

    /**
     * @param Magento_Core_Model_DataService_Config_Reader_Factory $readerFactory
     * @param Magento_Core_Model_Config_Modules_Reader
     */
    public function __construct(Magento_Core_Model_DataService_Config_Reader_Factory $readerFactory,
        Magento_Core_Model_Config_Modules_Reader $moduleReader
    ) {
        $this->_readerFactory = $readerFactory;
        $this->_moduleReader = $moduleReader;
        $this->_indexServiceCallNodes();
    }

    /**
     * Build an index of service calls nodes to avoid expensive xpath calls
     *
     * @return Magento_Core_Model_DataService_Config $this
     */
    private function _indexServiceCallNodes()
    {
        /** @var DOMElement $node */
        foreach ($this->getServiceCalls() as $node) {
            $this->_serviceCallNodes[$node->getAttribute('name')] = $node;
        }
        return $this;
    }

    /**
     * Reader object initialization.
     *
     * @return Magento_Core_Model_DataService_Config_Reader
     */
    protected function _getReader()
    {
        if (is_null($this->_reader)) {
            $serviceCallsFiles = $this->_getServiceCallsFiles();
            $this->_reader = $this->_readerFactory->createReader($serviceCallsFiles);
        }
        return $this->_reader;
    }

    /**
     * Retrieve list of service calls files from each module.
     *
     * @return array
     */
    protected function _getServiceCallsFiles()
    {
        return $this->_moduleReader->getConfigurationFiles('service_calls.xml');
    }

    /**
     * Get DOMXPath with loaded service calls inside.
     *
     * @return DOMXPath
     */
    protected function _getXPathServiceCalls()
    {
        $serviceCalls = $this->_getReader()->getServiceCallConfig();
        return new DOMXPath($serviceCalls);
    }

    /**
     * Return Service Calls.
     *
     * @return DOMNodeList
     */
    public function getServiceCalls()
    {
        return $this->_getXPathServiceCalls()->query(self::SERVICE_CALLS_XPATH);
    }

    /**
     * Get the class information for a given service call
     *
     * @param string $alias
     * @return array
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function getClassByAlias($alias)
    {
        //validate that service call is defined
        if (!isset($this->_serviceCallNodes[$alias])) {
            throw new InvalidArgumentException("Service call with name '{$alias}'  doesn't exist");
        }

        /** @var DOMElement $node */
        $node = $this->_serviceCallNodes[$alias];
        $methodArguments = array();

        /** @var DOMElement $child */
        foreach ($node->childNodes as $child) {
            if ($child instanceof DOMElement) {
                $methodArguments[$child->getAttribute('name')] = $child->nodeValue;
            }
        }

        $result = array(
            'class' => $node->getAttribute('service'),
            'retrieveMethod' => $node->getAttribute('method'),
            'methodArguments' => $methodArguments,
        );

        //validate that service attribute is defined
        if (!$result['class']) {
            throw new InvalidArgumentException("Invalid Service call {$alias}, "
                . 'service type must be defined in the "service" attribute');
        }

        //validate that retrieval method attribute is defined
        if (!$result['retrieveMethod']) {
            throw new LogicException("Invalid Service call {$alias}, "
                . "retrieval method must be defined for the service {$result['class']}");
        }

        return $result;
    }
}
