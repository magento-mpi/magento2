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
class Mage_Core_Model_Dataservice_Config implements Mage_Core_Model_Dataservice_Config_Interface
{
    /**
     * The global area in the config
     */
    const CONFIG_AREA = 'global';

    /**
     * The location in xml for the service_calls
     */
    const CONFIG_NODE = 'service_calls';

    /**
     * @var Varien_Simplexml_Element
     */
    protected $_simpleXml;

    /**
     * @var string
     */
    protected $_elementClass = 'Varien_Simplexml_Element';

    /**
     * @var Mage_Core_Model_ConfigInterface
     */
    protected $_config;

    /** @var \Mage_Core_Model_Config_Loader_Modules_File  */
    protected $_fileReader;

    /**
     * @param Mage_Core_Model_ConfigInterface $config
     * @param Mage_Core_Model_Config_Loader_Modules_File $fileReader
     */
    public function __construct(Mage_Core_Model_ConfigInterface $config,
        Mage_Core_Model_Config_Loader_Modules_File $fileReader
    ) {
        $this->_fileReader = $fileReader;
        $this->_config = $config;
        $this->init();
    }

    /**
     * @param $alias
     * @return array
     * @throws Mage_Core_Exception
     */
    public function getClassByAlias($alias)
    {
        $node = $this->_simpleXml->xpath("//service_call[@name='" . $alias . "']");

        if (count($node) == 0) {
            throw new Mage_Core_Exception('Service call with name "' . $alias . '" doesn\'t exist');
        }

        /** @var $node Mage_Core_Model_Config_Element */
        $node = current($node);

        $methodArguments = array();
        foreach ($node[0] as $child) {
            if ($child->getName() == 'arg') {
                $methodArguments[$child->getAttribute('name')] = (string)$child;
            }
        }

        $result = array(
            'class' => $node->getAttribute('service'),
            'retrieveMethod' => $node->getAttribute('method'),
            'methodArguments' => $methodArguments,
        );

        if (!$result['class']) {
            throw new Mage_Core_Exception('Invalid Service call ' . $alias
                . ', service type must be defined in the "service" attribute');
        }

        return $result;
    }

    /**
     * @return SimpleXMLElement
     */
    public function init()
    {
        $updatesRootPath = self::CONFIG_AREA . '/' . self::CONFIG_NODE;
        $sourcesRoot = $this->_config->getNode($updatesRootPath);

        /* Layout update files declared in configuration */
        $callsStr = '<calls />';
        if ($sourcesRoot) {
            $callsStr = $this->_getServiceCallConfig($sourcesRoot);
        }

        $this->_simpleXml = simplexml_load_string($callsStr, $this->_elementClass);
        return $this->_simpleXml;
    }

    /**
     * @param Mage_Core_Model_Config_Element $sourcesRoot
     * @return string
     */
    public function _getServiceCallConfig($sourcesRoot)
    {
        $sourceFiles = array();
        foreach ($sourcesRoot->children() as $sourceNode) {
            $sourceFiles[] = $this->_getServiceCallsFile($sourceNode);
        }

        $callsStr = '';
        foreach ($sourceFiles as $filename) {
            $fileStr = file_get_contents($filename);

            /** @var $fileXml Mage_Core_Model_Layout_Element */
            $fileXml = simplexml_load_string($fileStr, $this->_elementClass);
            $callsStr .= $fileXml->innerXml();
        }
        return '<calls>' . $callsStr . '</calls>';
    }

    /**
     * @param Mage_Core_Model_Config_Element $sourceNode
     * @return string
     * @throws Magento_Exception
     */
    protected function _getServiceCallsFile($sourceNode)
    {
        $file = (string)$sourceNode->file;
        if (!$file) {
            $sourceNodePath = $sourceNode->getName();
            throw new Magento_Exception(
                "Service calls instruction '{$sourceNodePath}' must specify file."
            );
        }

        $nameParts = array();
        if (strpos($file, '/') !== false) {
            $nameParts = explode('/', $file);
        } else {
            throw new Magento_Exception("Module is missing in Service calls configuration: '{$file}'");
        }
        $filename = $this->_fileReader->getModuleDir('etc', $nameParts[0]) . '/' . $nameParts[1];
        if (!is_readable($filename)) {
            throw new
            Magento_Exception("Service calls configuration file '{$filename}' doesn't exist or isn't readable.");
        }
        return $filename;
    }
}
