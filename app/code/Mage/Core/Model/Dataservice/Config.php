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

    const FILE_NAME = 'service_calls.xml';

    const ELEMENT_CLASS = 'Varien_Simplexml_Element';

    /** @var Mage_Core_Model_Config_Modules_Reader  */
    protected $_moduleReader;


    /**
     * @var Varien_Simplexml_Element
     */
    protected $_simpleXml;



    /**
     * @param Mage_Core_Model_Config_Modules_Reader $moduleReader
     */
    public function __construct(
        Mage_Core_Model_Config_Modules_Reader $moduleReader
    ) {
        $this->_moduleReader = $moduleReader;
    }


    /**
     * @param $alias
     * @return array
     * @throws Mage_Core_Exception
     */
    public function getClassByAlias($alias)
    {
        if ($this->_simpleXml === null) {
            $this->_simpleXml = $this->_loadServiceCallsIntoXmlElement();
        }

        $nodes = $this->_simpleXml->xpath("//service_call[@name='" . $alias . "']");

        if (count($nodes) == 0) {
            throw new Mage_Core_Exception('Service call with name "' . $alias . '" doesn\'t exist');
        }

        /** @var Mage_Core_Model_Config_Element $node */
        $node = current($nodes);

        $methodArguments = array();
        /** @var Mage_Core_Model_Config_Element $child */
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
     * Loads the service calls config from all the service_calls.xml files
     *
     * @return SimpleXMLElement
     */
    private function _loadServiceCallsIntoXmlElement()
    {

        /* Layout update files declared in configuration */
        $callsStr = $this->_getServiceCallConfig();

        $this->_simpleXml = simplexml_load_string($callsStr, self::ELEMENT_CLASS);
        return $this->_simpleXml;
    }

    /**
     * Reads all service calls files into one XML string with <calls> as the root
     *
     * @return string
     */
    private function _getServiceCallConfig()
    {
        $sourceFiles = $this->_getServiceCallsFiles();

        $callsStr = '';
        foreach ($sourceFiles as $filename) {
            $fileStr = file_get_contents($filename);

            /** @var $fileXml Mage_Core_Model_Layout_Element */
            $fileXml = simplexml_load_string($fileStr, self::ELEMENT_CLASS);
            $callsStr .= $fileXml->innerXml();
        }
        return '<calls>' . $callsStr . '</calls>';
    }

    /**
     * Returns array of files that contain service calls config
     *
     * @return array of files
     */
    private function _getServiceCallsFiles()
    {
        $files = $this->_moduleReader
            ->getModuleConfigurationFiles(self::FILE_NAME);
        return (array)$files;
    }
}
