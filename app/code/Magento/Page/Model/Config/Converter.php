<?php
/**
 * Page layout Config Converter
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Page_Model_Config_Converter implements Magento_Config_ConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function convert($source)
    {
        $pageLayouts = array();
        $xpath = new DOMXPath($source);
        /** @var $layout DOMNode */
        foreach ($xpath->query('/page_layouts/layout') as $layout) {
            $layoutAttributes = $layout->attributes;
            $id = $layoutAttributes->getNamedItem('id')->nodeValue;
            $isDefault = $layoutAttributes->getNamedItem('default');;
            $pageLayouts[$id]['code'] = $id;
            $pageLayouts[$id]['is_default'] = ($isDefault && $isDefault->nodeValue == 'true') ? 1 : 0;

            /** @var $layoutSubNode DOMNode */
            foreach ($layout->childNodes as $layoutSubNode) {
                switch ($layoutSubNode->nodeName) {
                    case 'label':
                    case 'template':
                    case 'layout_handle':
                        $pageLayouts[$id][$layoutSubNode->nodeName] = $layoutSubNode->nodeValue;
                        break;
                    default:
                        break;
                }
            }
        }
        return $pageLayouts;
    }
}
