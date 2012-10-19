<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Backend System Configuration layout converter.
 * Converts dom document to array
 *
 * @category    Mage
 * @package     Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Config_Structure_Converter
{
    /**
     * Map of single=>plural node names
     *
     * @var array
     */
    protected $nameMap = array(
        'tab' => 'tabs',
        'section' => 'sections',
        'group' => 'groups',
        'field' => 'fields'
    );

    /**
     * Retrieve DOMDocument as array
     *
     * @param DOMNode $root
     * @return array
     */
    public function convert(DOMNode $root)
    {
        $result = array();

        if ($root->hasAttributes())
        {
            $attrs = $root->attributes;

            foreach ($attrs as $attr)
                $result[$attr->name] = $attr->value;
        }

        $children = $root->childNodes;

        if ($children->length == 1)
        {
            $child = $children->item(0);

            if ($child->nodeType == XML_TEXT_NODE)
            {
                $result['_value'] = $child->nodeValue;

                if (count($result) == 1)
                    return $result['_value'];
                else
                    return $result;
            }
        }

        for($i = 0; $i < $children->length; $i++)
        {
            $child = $children->item($i);
            $nodeName = $child->nodeName;
            if (isset($this->nameMap[$child->nodeName])) {
                $nodeName = $this->nameMap[$child->nodeName];
            }

            if (!isset($result[$nodeName]))
                if ($child->nodeType == XML_TEXT_NODE)
                {
                } else {
                    if (isset($this->nameMap[$child->nodeName])) {
                        $result[$nodeName] = array($this->convert($child));
                    } else {
                        $result[$nodeName] = $this->convert($child);
                    }
                }
            else
            {
                $result[$nodeName][] = $this->convert($child);
            }
        }

        return $result;
    }
}
