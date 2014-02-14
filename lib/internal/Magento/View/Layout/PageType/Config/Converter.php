<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\View\Layout\PageType\Config;

class Converter implements \Magento\Config\ConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function convert($source)
    {
        $page_types = array();
        $xpath = new \DOMXPath($source);

        /** @var $widget \DOMNode */
        foreach ($xpath->query('/page_types/type') as $type) {
            $typeAttributes = $type->attributes;

            $id = $typeAttributes->getNamedItem('id')->nodeValue;
            $label = $typeAttributes->getNamedItem('label')->nodeValue;

            $pageArray = [
                "id" => $id,
                "label" => $label
            ];

            $page_types[$id] = $pageArray;
        }
        return $page_types;
    }
}
