<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\Handle\Data;

use Magento\View\Layout\Handle\AbstractHandle;
use Magento\View\LayoutInterface;
use Magento\View\Layout\Element;
use Magento\View\Layout\Handle\DataInterface;
use Magento\View\Layout\Handle\Render;

/**
 * @package Magento\View
 */
class Source extends AbstractHandle implements DataInterface
{
    /**
     * Container type
     */
    const TYPE = 'data';

    /**
     * @inheritdoc
     */
    public function parse(Element $layoutElement, LayoutInterface $layout, $parentName)
    {
        $elementName = $layoutElement->getAttribute('name');

        if (isset($elementName)) {
            $element = $this->parseAttributes($layoutElement);

            $element['type'] = self::TYPE;
            $element['name'] = $elementName;
            $layout->addElement($elementName, $element);

            // assign to parent element
            $this->assignToParentElement($element, $layout, $parentName);

            // parse children
            $this->parseChildren($layoutElement, $layout, $elementName);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function register(array $element, LayoutInterface $layout)
    {
        if (isset($element['class'])) {
            if (!class_exists($element['class'])) {
                throw new \Exception(__('Invalid Data Provider class name: ' . $element['class']));
            }

            $elementName = isset($element['name']) ? $element['name'] : null;
            $alias = isset($element['as']) ? $element['as'] : $elementName;
            $parent = isset($element['parent']) ? $element['parent'] : null;
            $layout->addDataSource($element['class'], $elementName, $parent, $alias);
        }

        return $this;
    }
}
