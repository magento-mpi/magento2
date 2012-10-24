<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * System configuration migration parser
 */

class Tools_Migration_System_Configuration_Parser
{
    /**
     * Parse dom document
     *
     * @param DOMDocument $dom
     * @return array
     */
    public function parse(DOMDocument $dom)
    {
        $result = array();
        if ($dom->hasChildNodes()) {
            foreach ($dom->childNodes as $child) {
                if (XML_COMMENT_NODE == $child->nodeType) {
                    $result['comment'] = $child->nodeValue;
                } else if (XML_ELEMENT_NODE == $child->nodeType && 'config' == $child->nodeName) {
                    $result = array_merge($result, $this->_parseNode($child));
                }
            }
        }
        return $result;
    }

    /**
     * Parse dom node
     *
     * @param DOMNode $node
     * @return array
     */
    protected function _parseNode(DOMNode $node = null)
    {
        // return empty array if dom is blank
        if (is_null($node)) {
            return array();
        }

        if (!$node->hasChildNodes()) {
            $result = $node->nodeValue;
        } else {
            $result = array();
            foreach ($node->childNodes as $childNode) {
                // how many of these child nodes do we have?
                // this will give us a clue as to what the result structure should be
                $oChildNodeList = $node->getElementsByTagName($childNode->nodeName);
                $iChildCount = 0;
                // there are x number of childs in this node that have the same tag name
                // however, we are only interested in the # of siblings with the same tag name
                foreach ($oChildNodeList as $oNode) {
                    if ($oNode->parentNode->isSameNode($childNode->parentNode)) {
                        $iChildCount++;
                    }
                }
                $mValue = $this->_parseNode($childNode);
                $sKey   = ($childNode->nodeName{0} == '#') ? 0 : $childNode->nodeName;
                $mValue = (is_array($mValue) && !empty($mValue)) ? $mValue[$childNode->nodeName] : $mValue;
                if ($sKey == 0 && is_array($mValue) && empty($mValue)) {
                    continue;
                }
                // how many of thse child nodes do we have?
                if ($iChildCount > 1) {  // more than 1 child - make numeric array
                    $result[$sKey][] = $mValue;
                } else {
                    $result[$sKey] = $mValue;
                }
            }
            // if the child is <foo>bar</foo>, the result will be array(bar)
            // make the result just 'bar'
            if (count($result) == 1 && isset($result[0]) && !is_array($result[0]['@value'])) {
                $result = $result[0];
            }
        }

        // get our attributes if we have any
        $attributes = array();
        if ($node->hasAttributes()) {
            foreach ($node->attributes as $oAttrNode) {
                // retain namespace prefixes
                $attributes["{$oAttrNode->nodeName}"] = $oAttrNode->nodeValue;
            }
        }

        if (count($attributes)) {
            if (!is_array($result)) {
                $result = (trim($result)) ? array('@value' => $result) : array();
            }
            $fResult = array($node->nodeName => array_merge($result, array('@attributes' => $attributes)));
        } else {
            if (is_array($result)) {
                $fResult = array($node->nodeName => $result);
            } else {
                $fResult = (trim($result)) ? array($node->nodeName => array('@value' => $result)) : array();
            }
        }

        return $fResult;
    }
}