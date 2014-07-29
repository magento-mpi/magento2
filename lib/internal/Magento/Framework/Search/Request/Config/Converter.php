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
        $request = [];
        foreach ($requestNodes as $requestNode) {
            /** @var \DOMElement $requestNode */
            $name = $requestNode->getAttribute('name');
            $request[$name] = $this->convertRequest($requestNode);
        }

    }

    /**
     * Convert node to request
     *
     * @param \DOMElement $requestNode
     * @return RequestInterface
     */
    private function convertRequest(\DOMElement $requestNode)
    {
    }
}
