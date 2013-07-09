<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Layout_Reader
{
    /**
     * @var SimpleXMLElement
     */
    private $_layoutRootNode;

    /**
     * @param SimpleXMLElement $layoutRootNode
     */
    public function __construct(SimpleXMLElement $layoutRootNode)
    {
        $this->_layoutRootNode = $layoutRootNode;
    }

    /**
     * Retrieve layout handles
     *
     * @return array|Layout_Handle[]
     */
    public function getHandles()
    {
        $result = array();
        /** @var SimpleXMLElement $handleNode */
        foreach ($this->_layoutRootNode as $handleNode) {
            $result[] = new Layout_Handle($handleNode);
        }
        return $result;
    }
}
