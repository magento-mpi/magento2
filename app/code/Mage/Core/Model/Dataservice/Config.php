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
    //const CONFIG_AREA = 'global';



    const ELEMENT_CLASS = 'Varien_Simplexml_Element';

    /**
     * @var Varien_Simplexml_Element
     */
    protected $_simpleXml;

    /** @var Mage_Core_Dataservice_Config_Reader */
    protected $_configReader;

    /**
     * @param Mage_Core_Model_Config_Modules_Reader $moduleReader
     */
    public function __construct(
        Mage_Core_Model_Dataservice_Config_Reader $configReader
    ) {
        $this->_configReader = $configReader;
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
        $callsStr = $this->_configReader->getServiceCallConfig();

        $this->_simpleXml = simplexml_load_string($callsStr, self::ELEMENT_CLASS);
        return $this->_simpleXml;
    }


}
