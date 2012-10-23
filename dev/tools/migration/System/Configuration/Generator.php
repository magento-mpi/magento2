<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    tools
 * @copyright  {copyright}
 * @license    {license_link}
 */


class Tools_Migration_System_Configuration_Generator
{
    /**
     * @var Tools_Migration_System_FileManager
     */
    protected $_fileManager;

    /**
     * @var Tools_Migration_System_Configuration_Formatter
     */
    protected $_xmlFormatter;

    /**
     * @var Tools_Migration_System_Configuration_LoggerAbstract
     */
    protected $_logger;

    public function __construct(
        Tools_Migration_System_Configuration_Formatter $xmlFormatter,
        Tools_Migration_System_FileManager $fileManager,
        Tools_Migration_System_Configuration_LoggerAbstract $logger
    ) {
        $this->_fileManager = $fileManager;
        $this->_xmlFormatter = $xmlFormatter;
        $this->_logger = $logger;
    }

    /**
     *
     * @param string $fileName
     * @param array $configuration
     */
    public function createConfiguration($fileName, array $configuration)
    {
        $domDocument = $this->_createDOMDocument($configuration);
        $output = $this->_xmlFormatter->parseString($domDocument->saveXml(), array(
            'indent' => true,
            'input-xml' => true,
            'output-xml' => true,
            'add-xml-space' => false,
            'indent-spaces' => 4,
            'wrap' => 1000
        ));
        $newFileName = $this->_getPathToSave($fileName);
        $this->_fileManager->write($newFileName, $output);
        $this->_logger->add($fileName . ' was converted into ' . $newFileName);
    }

    /**
     * @param array $configuration
     * @return DOMDocument
     */
    protected function _createDOMDocument(array $configuration)
    {
        $dom = new DOMDocument();
        $dom->appendChild($dom->createComment($configuration['comment']));
        $configElement = $dom->createElement('config');
        $systemElement = $dom->createElement('system');
        $configElement->appendChild($systemElement);
        $dom->appendChild($configElement);

        foreach ($configuration['nodes'] as $config) {
            $element = $this->_createElement($config, $dom);
            $systemElement->appendChild($element);
        }
        return $dom;
    }

    /**
     * Create element
     *
     * @param array $config
     * @param DOMDocument $dom
     * @return DOMElement
     */
    protected function _createElement($config, DOMDocument $dom)
    {
        $element = $dom->createElement($config['nodeName'], isset($config['value']) ? $config['value'] : '');
        $attributes = isset($config['@attributes']) ? $config['@attributes'] : array();
        foreach ($attributes as $attributeName => $attributeValue) {
            $element->setAttribute($attributeName, $attributeValue);
        }

        $parameters = isset($config['parameters']) ? $config['parameters'] : array();
        foreach ($parameters as $paramConfig) {
            $childElement = $dom->createElement(
                $paramConfig['name'],
                isset($paramConfig['value']) ? $paramConfig['value'] : ''
            );

            $paramAttributes = isset($paramConfig['@attributes']) ? $paramConfig['@attributes'] : array();
            foreach ($paramAttributes as $attributeName => $attributeValue) {
                $childElement->setAttribute($attributeName, $attributeValue);
            }

            if (isset($paramConfig['subConfig'])) {
                foreach ($paramConfig['subConfig'] as $subConfig) {
                    $childElement->appendChild($this->_createElement($subConfig, $dom));
                }
            }
            $element->appendChild($childElement);
        }

        if (isset($config['subConfig'])) {
            foreach ($config['subConfig'] as $subConfig) {
                $element->appendChild($this->_createElement($subConfig, $dom));
            }
        }

        return $element;
    }

    /**
     * Get new path to system configuration file
     *
     * @param $fileName
     * @return string
     */
    protected function _getPathToSave($fileName)
    {
        return dirname($fileName) . 'adminhtml' . DIRECTORY_SEPARATOR . 'system.xml';
    }
}
