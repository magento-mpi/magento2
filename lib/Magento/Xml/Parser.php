<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Xml
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Xml;

class Parser
{
    /** @var \DOMDocument|null */
    protected $_dom = null;

    /** @var \DOMDocument */
    protected $_currentDom;

    /** @var array */
    protected $_content = array();

    public function __construct()
    {
        $this->_dom = new \DOMDocument;
        $this->_currentDom = $this->_dom;
        return $this;
    }

    /**
     * @return \DOMDocument|null
     */
    public function getDom()
    {
        return $this->_dom;
    }

    /**
     * @return \DOMDocument
     */
    protected function _getCurrentDom()
    {
        return $this->_currentDom;
    }

    /**
     * @param \DOMDocument $node
     * @return $this
     */
    protected function _setCurrentDom($node)
    {
        $this->_currentDom = $node;
        return $this;
    }

    /**
     * @return array
     */
    public function xmlToArray()
    {
        $this->_content = $this->_xmlToArray();
        return $this->_content;
    }

    /**
     * @param bool $currentNode
     * @return array
     */
    protected function _xmlToArray($currentNode=false)
    {
        if (!$currentNode) {
            $currentNode = $this->getDom();
        }
        $content = array();
        foreach ($currentNode->childNodes as $node) {
            switch ($node->nodeType) {
                case XML_ELEMENT_NODE:

                    $value = null;
                    if ($node->hasChildNodes()) {
                        $value = $this->_xmlToArray($node);
                    }
                    $attributes = array();
                    if ($node->hasAttributes()) {
                        foreach($node->attributes as $attribute) {
                            $attributes += array($attribute->name=>$attribute->value);
                        }
                        $value = array('_value'=>$value, '_attribute'=>$attributes);
                    }
                    if (isset($content[$node->nodeName])) {
                        if (!isset($content[$node->nodeName][0]) || !is_array($content[$node->nodeName][0])) {
                            $oldValue = $content[$node->nodeName];
                            $content[$node->nodeName] = array();
                            $content[$node->nodeName][] = $oldValue;
                        }
                        $content[$node->nodeName][] = $value;
                    } else {
                        $content[$node->nodeName] = $value;
                    }
                    break;
                case XML_TEXT_NODE:
                    if (trim($node->nodeValue)) {
                        $content = $node->nodeValue;
                    }
                    break;
            }
        }
        return $content;
    }

    /**
     * @param string $file
     * @return $this
     */
    public function load($file)
    {
        $this->getDom()->load($file);
        return $this;
    }

    /**
     * @param string $string
     * @return $this
     */
    public function loadXML($string)
    {
        $this->getDom()->loadXML($string);
        return $this;
    }

}
