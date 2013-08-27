<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Backend_Model_Menu_Config_Converter implements Magento_Config_ConverterInterface
{
    /**
     * @param mixed $dom
     * @return array
     */
    public function convert($dom)
    {
        $extractedData = array();

        $attributeNamesList = array(
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
        );
        $xpath = new DOMXPath($dom);
        $nodeList = $xpath->query('/config/menu/*');
        for ($i = 0; $i < $nodeList->length; $i++) {
            $item = array();
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
