<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Framework\View\Layout\PageType\Config;

class Converter implements \Magento\Framework\Config\ConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function convert($source)
    {
        $pageTypes = array();
        $xpath = new \DOMXPath($source);

        /** @var $widget \DOMNode */
        foreach ($xpath->query('/page_types/type') as $type) {
            $typeAttributes = $type->attributes;

            $id = $typeAttributes->getNamedItem('id')->nodeValue;
            $label = $typeAttributes->getNamedItem('label')->nodeValue;

            $pageArray = array("id" => $id, "label" => $label);

            $pageTypes[$id] = $pageArray;
        }
        return $pageTypes;
    }
}
