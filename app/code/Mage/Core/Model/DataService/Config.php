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
class Mage_Core_Model_DataService_Config implements Mage_Core_Model_DataService_ConfigInterface
{
    /** @var Mage_Core_Model_DataService_Config_Reader */
    protected $_configReader;

    /**
     * @param Mage_Core_Model_DataService_Config_Reader $configReader
     */
    public function __construct(
        Mage_Core_Model_DataService_Config_Reader $configReader
    ) {
        $this->_configReader = $configReader;
    }


    /**
     * @param $alias
     * @return array
     * @throws InvalidArgumentException
     */
    public function getClassByAlias($alias)
    {
        $serviceCallConfig = $this->_configReader->getServiceCallConfig();
        $nodes = $serviceCallConfig->getXpath("//service_call[@name='" . $alias . "']");

        if (!$nodes || count($nodes) == 0) {
            throw new InvalidArgumentException('Service call with name "' . $alias . '" doesn\'t exist');
        }

        /** @var Mage_Core_Model_Config_Element $node */
        $node = end($nodes);

        $methodArguments = array();
        /** @var Mage_Core_Model_Config_Element $child */
        foreach ($node as $child) {
            $methodArguments[$child->getAttribute('name')] = (string)$child;
        }

        $result = array(
            'class' => $node->getAttribute('service'),
            'retrieveMethod' => $node->getAttribute('method'),
            'methodArguments' => $methodArguments,
        );

        if (!$result['class']) {
            throw new InvalidArgumentException('Invalid Service call ' . $alias
                . ', service type must be defined in the "service" attribute');
        }

        return $result;
    }
}
