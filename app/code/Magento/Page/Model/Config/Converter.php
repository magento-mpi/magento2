<?php
/**
 * Page layout Config Converter
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Page\Model\Config;

class Converter implements \Magento\Config\ConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function convert($source)
    {
        $pageLayouts = array();
        $xpath = new \DOMXPath($source);

        $defaultLayout = $xpath->query('/page_layouts/layouts')->item(0)->getAttribute('default');

        /** @var $layout DOMNode */
        foreach ($xpath->query('/page_layouts/layouts/layout') as $layout) {
            $layoutAttributes = $layout->attributes;
            $id = $layoutAttributes->getNamedItem('id')->nodeValue;
            $pageLayouts[$id]['code'] = $id;
            $pageLayouts[$id]['is_default'] = ($defaultLayout === $id) ? 1 : 0;

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
