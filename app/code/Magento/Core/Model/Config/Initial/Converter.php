<?php
/**
 * Initial configuration data converter. Converts \DOMDocument to array
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config\Initial;

class Converter implements \Magento\Config\ConverterInterface
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
     * @var array
     */
    protected $_metadata = array();

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
     * @param \DOMDocument $source
     * @return array
     */
    public function convert($source)
    {
        $output = array();
        $xpath = new \DOMXPath($source);
        $this->_metadata = array();

        /** @var $node \DOMNode */
        foreach ($xpath->query(implode(' | ', $this->_nodeMap)) as $node) {
            $output = array_merge($output, $this->_convertNode($node));
        }
        return array('data' => $output, 'metadata' => $this->_metadata);
    }

    /**
     * Convert node oto array
     *
     * @param \DOMNode $node
     * @param string $path
     * @return array|string|null
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _convertNode(\DOMNode $node, $path = '')
    {
        $output = array();
        if ($node->nodeType == XML_ELEMENT_NODE) {
            if ($node->hasAttributes()) {
                $backendModel = $node->attributes->getNamedItem('backend_model');
                if ($backendModel) {
                    $this->_metadata[$path] = array('backendModel' => $backendModel->nodeValue);
                }
            }
            $nodeData = array();
            /** @var $childNode \DOMNode */
            foreach ($node->childNodes as $childNode) {
                $childrenData = $this->_convertNode($childNode, ($path ? $path . '/' : '') . $childNode->nodeName);
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
