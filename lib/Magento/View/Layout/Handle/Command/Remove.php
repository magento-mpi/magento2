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
     * @var int
     */
    private $inc = 0;

    /**
     * @param Element $layoutElement
     * @param LayoutInterface $layout
     * @param string $parentName
     * @return Remove
     */
    public function parse(Element $layoutElement, LayoutInterface $layout, $parentName)
    {
        $element = $this->parseAttributes($layoutElement);

        $element['type'] = self::TYPE;

        $elementName = isset($element['name']) ? $element['name'] : ('Command-Move-' . $this->inc++);
        $layout->addElement($elementName, $element);

        if (isset($parentName)) {
            $layout->setChild($parentName, $elementName, $elementName);
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
        $elementName = isset($element['element']) ? $element['element'] : null;
        if (isset($elementName)) {
            $layout->unsetElement($elementName);
        }

        $alias = $layout->getChildAlias($parentName, $element['name']);
        $layout->unsetChild($parentName, $alias);

        return $this;
    }
}
