<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Layout_Analyzer
{
    /**
     * @var Layout_Merger
     */
    private $_merger;

    /**
     * @var Xml_Formatter
     */
    private $_formatter;

    /**
     * @var string
     */
    private $_template;

    /**
     * @param Layout_Merger $merger
     * @param Xml_Formatter $formatter
     * @param string $template
     */
    public function __construct(Layout_Merger $merger, Xml_Formatter $formatter, $template = '%s')
    {
        $this->_merger = $merger;
        $this->_formatter = $formatter;
        $this->_template = $template;
    }

    /**
     * Combine layout handles with the same names and return their well-formatted XML contents
     *
     * @param array|string[] $files
     * @return array Format: array('<handle_name>' => '<handle_xml>', ...)
     * @throws Exception
     */
    public function aggregateHandles(array $files)
    {
        $handles = array();
        foreach ($files as $file) {
            $rootNode = @simplexml_load_file($file);
            if ($rootNode === false) {
                throw new Exception("Unable to read file '$file'.");
            }
            $layout = new Layout_Reader($rootNode);
            $handles = array_merge($handles, $layout->getHandles());
        }

        $mergedLayout = new Layout_Reader(simplexml_load_string($this->_merger->merge($handles)));

        $result = array();
        foreach ($mergedLayout->getHandles() as $handle) {
            $result[$handle->getName()] = $this->_formatter->format(sprintf($this->_template, $handle->renderXml()));
        }
        return $result;
    }

    public function getTemplate()
    {
        return $this->_template;
    }

    public function getHandles($file)
    {
        $rootNode = @simplexml_load_file($file);
        if ($rootNode === false) {
            throw new Exception("Unable to read file '$file'.");
        }

        $result = array();
        $handleNodes = $rootNode->children();
        foreach ($handleNodes as $handleNode) {
            $result[] = $handleNode->getName();
        }
        return $result;
    }
}
