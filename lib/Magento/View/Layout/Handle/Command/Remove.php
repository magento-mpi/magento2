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

class Remove implements Command
{
    /**
     * Container type
     */
    const TYPE = 'remove';

    private $inc = 0;

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
        $elementName = isset($element['name']) ? $element['name'] : ('Command-Move-' . $this->inc++);

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
     * @return Remove
     */
    public function register(array $element, Layout $layout, $parentName)
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
