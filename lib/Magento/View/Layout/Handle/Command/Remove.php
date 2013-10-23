<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\Handle\Command;

use Magento\View\LayoutInterface;
use Magento\View\Layout\Element;
use Magento\View\Layout\Handle;
use Magento\View\Layout\Handle\CommandInterface;
use Magento\View\Layout\Handle\Render;

class Remove extends Handle\AbstractHandle implements CommandInterface
{
    /**
     * Container type
     */
    const TYPE = 'remove';

    /**
     * @param Element $layoutElement
     * @param LayoutInterface $layout
     * @param string $parentName
     * @return Remove
     */
    public function parse(Element $layoutElement, LayoutInterface $layout, $parentName)
    {
        $element = $this->parseAttributes($layoutElement);
        $element['element'] = $element['name'];
        $element['name'] = 'Command-Move-' . $this->nameIncrement++;
        $element['type'] = self::TYPE;

        $layout->addElement($element['name'], $element);

        if (isset($parentName)) {
            $layout->setChild($parentName, $element['name'], $element['name']);
        }

        return $this;
    }

    /**
     * @param array $element
     * @param LayoutInterface $layout
     * @param string $parentName
     * @return Remove
     */
    public function register(array $element, LayoutInterface $layout, $parentName)
    {
        if (isset($element['element'])) {
            $layout->unsetElement($element['element']);
        }

        $layout->unsetElement($element['name']);

        return $this;
    }
}
