<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\Handle\Command;

use Magento\View\Context;
use Magento\View\Layout;
use Magento\View\Layout\Element;
use Magento\View\Layout\Handle;
use Magento\View\Layout\Handle\Command;
use Magento\View\Layout\Handle\Render;

class Move implements Command
{
    /**
     * Container type
     */
    const TYPE = 'move';

    private static $inc = 0;

    /**
     * @param Element $layoutElement
     * @param Layout $layout
     * @param string $parentName
     * @return $this
     */
    public function parse(Element $layoutElement, Layout $layout, $parentName)
    {
        $element = array();
        foreach ($layoutElement->attributes() as $attributeName => $attribute) {
            if ($attribute) {
                $element[$attributeName] = (string)$attribute;
            }
        }
        $element['type'] = self::TYPE;
        $elementName = isset($element['name']) ? $element['name'] : ('Command-Move-' . self::$inc++);

        $layout->addElement($elementName, $element);
        if (isset($parentName)) {
            $layout->setChild($parentName, $elementName, $elementName);
        }

        return $this;
    }

    /**
     * @param array $element
     * @param Layout $layout
     * @param string $parentName
     * @return Move
     */
    public function register(array $element, Layout $layout, $parentName)
    {
        $elementName = isset($element['element']) ? $element['element'] : null;
        if (isset($elementName) && isset($parentName)) {
            if ($layout->getElement($element['element'])) {
                $elementParentName = $layout->getParentName($elementName);
                $layout->unsetChild($elementParentName, $elementName);

                if (isset($element['destination'])) {
                    $alias = isset($element['as']) ? $element['as'] : $elementName;
                    $layout->setChild($element['destination'], $elementName, $alias);
                }
            }
        }

        $alias = $layout->getChildAlias($parentName, $element['name']);
        $layout->unsetChild($parentName, $alias);

        return $this;
    }
}
