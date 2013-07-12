<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Layout_Handle
{
    /**
     * @var SimpleXMLElement
     */
    private $_handleNode;

    /**
     * @param SimpleXMLElement $handleNode
     */
    public function __construct(SimpleXMLElement $handleNode)
    {
        $this->_handleNode = $handleNode;
    }

    /**
     * Retrieve handle name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_handleNode->getName();
    }

    /**
     * Render layout handle attributes
     *
     * @return string
     */
    public function renderAttributes()
    {
        $result = '';
        foreach ($this->_handleNode->attributes() as $attrName => $attrValue) {
            $result .= ' ' . $attrName . '="' . $attrValue . '"';
        }
        return $result;
    }

    /**
     * Render layout handle inner XML
     *
     * @return string
     */
    public function renderInnerXml()
    {
        $result = '';
        /** @var $childNode SimpleXMLElement */
        foreach ($this->_handleNode->children() as $childNode) {
            $result .= $childNode->asXml();
        }
        return $result;
    }

    /**
     * Render layout handle XML
     *
     * @return string
     */
    public function renderXml()
    {
        return $this->_handleNode->asXML();
    }
}
