<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Backend\Model\Menu\Config;

class Converter implements \Magento\Framework\Config\ConverterInterface
{
    /**
     * @param mixed $dom
     * @return array
     */
    public function convert($dom)
    {
        $extractedData = [];

        $attributeNamesList = [
            'id',
            'title',
            'toolTip',
            'module',
            'sortOrder',
            'action',
            'parent',
            'resource',
            'dependsOnModule',
            'dependsOnConfig',
        ];
        $xpath = new \DOMXPath($dom);
        $nodeList = $xpath->query('/config/menu/*');
        for ($i = 0; $i < $nodeList->length; $i++) {
            $item = [];
            $node = $nodeList->item($i);
            $item['type'] = $node->nodeName;
            foreach ($attributeNamesList as $name) {
                if ($node->hasAttribute($name)) {
                    $item[$name] = $node->getAttribute($name);
                }
            }
            $extractedData[] = $item;
        }
        return $extractedData;
    }
}
