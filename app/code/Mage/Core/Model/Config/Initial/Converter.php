<?php
/**
 * Initial configuration data converter. Converts DOMDocument to array
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Config_Initial_Converter implements Magento_Config_ConverterInterface
{
    /**
     * Node path to process
     *
     * @var array
     */
    protected $_nodeMap = array(
        'default'  => '/config/default',
        'stores'   => '/config/stores',
        'websites' => '/config/websites',
    );

    /**
     * @param array $nodeMap
     */
    public function __construct(array $nodeMap = array())
    {
        $this->_nodeMap = array_merge($this->_nodeMap, $nodeMap);
    }

    /**
     * Convert config data
     *
     * @param DOMDocument $source
     * @return array
     */
    public function convert($source)
    {
        $output = array();
        $xpath = new DOMXPath($source);
        /** @var $node DOMNode */
        foreach ($xpath->query(implode(' | ', $this->_nodeMap)) as $node) {
            $output = array_merge($output, $this->_convertNode($node));
        }
        return $output;
    }

    /**
     * Convert node oto array
     *
     * @param DOMNode $node
     * @return array|string|null
     */
    protected function _convertNode(DOMNode $node)
    {
        $output = array();
        if ($node->nodeType == XML_ELEMENT_NODE) {
            $nodeData = array();
            foreach ($node->childNodes as $childNode) {
                $childrenData = $this->_convertNode($childNode);
                if ($childrenData == null) {
                    continue;
                }
                if (is_array($childrenData)) {
                    $nodeData = array_merge($nodeData, $childrenData);
                } else {
                    $nodeData = $childrenData;
                }
            }
            if (is_array($nodeData) && empty($nodeData)) {
                $nodeData = null;
            }
            $output[$node->nodeName] = $nodeData;
        } elseif ($node->nodeType == XML_CDATA_SECTION_NODE
            || ($node->nodeType == XML_TEXT_NODE && trim($node->nodeValue) != '')
        ) {
            return $node->nodeValue;
        }

        return $output;
    }
}
