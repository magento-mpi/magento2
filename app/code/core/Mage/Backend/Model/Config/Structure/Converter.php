<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Config_Structure_Converter
{
    /**
     * Map of single=>plural sub-node names per node
     *
     * E.G. first element makes all 'tab' nodes be renamed to 'tabs' in system node.
     *
     * @var array
     */
    protected $nameMap = array(
        'system' => array('tab' => 'tabs', 'section' => 'sections'),
        'section' => array('group' => 'groups'),
        'group' => array('field' => 'fields'),
        'depends' => array('field' => 'fields'),
    );

    /**
     * Retrieve DOMDocument as array
     *
     * @param DOMNode $root
     * @return mixed
     */
    public function convert(DOMNode $root)
    {
        $result = $this->_processAttributes($root);

        $children = $root->childNodes;

        $processedSubLists = array();
        for ($i = 0; $i < $children->length; $i++) {
            $child = $children->item($i);
            $childName = $child->nodeName;
            $convertedChild = array();

            switch ($child->nodeType) {
                case XML_COMMENT_NODE:
                    continue 2;
                    break;

                case XML_TEXT_NODE:
                    if ($children->length && trim($child->nodeValue, "\n ") === '') {
                        continue 2;
                    }
                    $childName = 'value';
                    $convertedChild = $child->nodeValue;
                    break;

                case XML_CDATA_SECTION_NODE:
                    $childName = 'value';
                    $convertedChild = $child->nodeValue;
                    break;

                default:
                    /** @var $child DOMElement */
                    if ($childName == 'attribute') {
                        $childName = $child->getAttribute('type');
                    }
                    $convertedChild = $this->convert($child);
                    break;
            }

            if (array_key_exists($root->nodeName, $this->nameMap)
                && array_key_exists($child->nodeName, $this->nameMap[$root->nodeName])) {
                $childName = $this->nameMap[$root->nodeName][$child->nodeName];
                $processedSubLists[] = $childName;
            }

            if (in_array($childName, $processedSubLists)) {
                if (is_array($convertedChild) && array_key_exists('id', $convertedChild)) {
                    $result[$childName][$convertedChild['id']] = $convertedChild;
                } else {
                    $result[$childName][] = $convertedChild;
                }
            } else if (array_key_exists($childName, $result)) {
                $result[$childName] = array($result[$childName], $convertedChild);
                $processedSubLists[] = $childName;
            } else {
                $result[$childName] = $convertedChild;
            }
        }

        if (count($result) == 1 && array_key_exists('value', $result)) {
            $result = $result['value'];
        }

        return $result;
    }

    /**
     * Process element attributes
     * 
     * @param DOMNode $root
     * @return array
     */
    protected function _processAttributes(DOMNode $root)
    {
        $result = array();

        if ($root->hasAttributes()) {
            $attributes = $root->attributes;
            foreach ($attributes as $attribute) {
                if ($root->nodeName == 'attribute' && $attribute->name == 'type') {
                    continue;
                }
                $result[$attribute->name] = $attribute->value;
            }
            return $result;
        }
        return $result;
    }
}
