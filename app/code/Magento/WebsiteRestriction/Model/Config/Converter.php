<?php
/**
 *  Converter of website restrictions configuration from \DOMDocument to tree array
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\WebsiteRestriction\Model\Config;

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
        $output = [];
        /** @var \DOMNodeList $actions */
        $actions = $source->getElementsByTagName('action');
        /** @var DOMNode $actionConfig */
        foreach ($actions as $actionConfig) {
            $actionPath = $actionConfig->attributes->getNamedItem('path')->nodeValue;
            $type = $actionConfig->attributes->getNamedItem('type')->nodeValue;
            $output[$type][] = $actionPath;
        }
        return $output;
    }
}
