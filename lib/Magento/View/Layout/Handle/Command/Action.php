<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\Handle\Command;

use Magento\View\Layout;
use Magento\View\Layout\Element;
use Magento\View\Layout\Handle;
use Magento\View\Layout\Handle\Command;

class Action implements Command
{
    /**
     * Container type
     */
    const TYPE = 'action';

    private static $inc = 0;

    /**
     * @param Element $layoutElement
     * @param Layout $layout
     * @param string $parentName
     * @return Action
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
        $elementName = isset($element['name']) ? $element['name'] : ('Command-Action-' . self::$inc++);

        $arguments = array();
        foreach ($layoutElement as $argument) {
            /** @var $argument Element */
            $name = $argument->getAttribute('name');
            $value = (string) $argument;
            $arguments[$name] = $value;
        }
        $element['arguments'] = $arguments;

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
     * @return Action
     */
    public function register(array $element, Layout $layout, $parentName)
    {
        $method = isset($element['method']) ? $element['method'] : null;
        if (isset($method) && isset($parentName)) {
            $arguments = isset($element['arguments']) ? $element['arguments'] : array();
            $block = $layout->getBlock($parentName);
            if (isset($block)) {
                call_user_func_array(array($block, $method), $arguments);
            }
        }

        $alias = $layout->getChildAlias($parentName, $element['name']);
        $layout->unsetChild($parentName, $alias);

        return $this;
    }
}
