<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Mage_Widget_Model_Config_Converter implements Magento_Config_ConverterInterface
{
    /** @var  Magento_Simplexml_Config */
    protected $_factory;

    /**
     * Constructor
     *
     * @param Magento_Simplexml_Config_Factory $factory
     */
    public function __construct(
        Magento_Simplexml_Config_Factory $factory
    ) {
        $this->_factory = $factory;
    }

    /**
     * Convert dom node tree to magneto xml config string
     *
     * @param DOMDocument $source
     * @return array
     */
    public function convert($source)
    {
        /** @var Magento_Simplexml_Config $nodeListData */
        $nodeListData = $this->_factory->create();
        $nodeListData->loadDom($source);
        $node = $nodeListData->getNode();
        $nodeArray = $this->_toArray($node);
        return is_array($nodeArray) ? $nodeArray : array($nodeArray);
    }

    /**
     * Returns the node and children as an array
     *
     * @param Magento_Simplexml_Element $node
     * @return array|string
     */
    protected function _toArray($node)
    {
        $result = array();

        // add attributes
        foreach ($node->attributes() as $attributeName => $attribute) {
            if ($attribute) {
                $result['@'][$attributeName] = (string)$attribute;
            }
        }

        // add children values
        if ($node->hasChildren()) {
            /** @var Magento_Simplexml_Element $child */
            foreach ($node->children() as $childName => $child) {
                $result[$childName][] = $this->_toArray($child);
            }
        } else {
            if (empty($result)) {
                // return as string, if nothing was found
                $result = (string) $node;
            } else {
                // value has zero key element
                $result[0] = (string) $node;
            }
        }
        return $result;
    }
}
