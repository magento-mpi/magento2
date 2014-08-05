<?php
/**
 * Search Request xml converter
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Request\Config;

class Converter implements \Magento\Framework\Config\ConverterInterface
{
    /**
     * Convert config
     *
     * @param \DOMDocument $source
     * @return array
     */
    public function convert($source)
    {
        /** @var \DOMNodeList $requestNodes */
        $requestNodes = $source->getElementsByTagName('request');
        $requests = [];
        foreach ($requestNodes as $requestNode) {
            $simpleXmlNode = simplexml_import_dom($requestNode);
            /** @var \DOMElement $requestNode */
            $name = $requestNode->getAttribute('query');
            $request = $this->mergeAttributes((array)$simpleXmlNode);
            $request['queries'] = $this->convertNodes($simpleXmlNode->queries, 'name');
            $request['filters'] = $this->convertNodes($simpleXmlNode->filters, 'name');
            //$request['aggregation'] = $this->convertNodes($simpleXmlNode->aggregation, 'name');
            $requests[$name] = $request;
        }
        return $requests;
    }

    /**
     * Merge attributes in node data
     *
     * @param $data
     * @return array
     */
    protected function mergeAttributes($data)
    {
        if (isset($data['@attributes'])) {
            $data = array_merge($data, $data['@attributes']);
            unset($data['@attributes']);
        }
        return $data;
    }

    /**
     * Deep converting simlexml element to array
     *
     * @param \SimpleXMLElement $node
     * @return array
     */
    protected function convertToArray(\SimpleXMLElement $node) {
        return $this->mergeAttributes(json_decode(json_encode($node), true));
    }

    /**
     * Convert nodes to array
     *
     * @param \SimpleXMLElement $nodes
     * @param $name
     * @return array
     */
    protected function convertNodes(\SimpleXMLElement $nodes, $name)
    {
        $list = [];
        /** @var \SimpleXMLElement $node */
        foreach ($nodes->children() as $node) {
            $element = $this->convertToArray($node->attributes());
            if (count($node->children()) > 0 ) {
                foreach ($node->children() as $child) {
                    $element[$child->getName()][] = $this->convertToArray($child);
                }
            }
            $type = (string)$node->attributes('xsi', true)['type'];
            if (!empty($type)) {
                $element['type'] = $type;
            }

            $list[$element[$name]] = $element;
        }
        return $list;
    }
}
