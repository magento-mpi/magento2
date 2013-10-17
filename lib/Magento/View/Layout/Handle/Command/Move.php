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

    /**
     * @param Element $layoutElement
     * @param Layout $layout
     * @param array $parentNode
     */
    public function parse(Element $layoutElement, Layout $layout, array & $parentNode = null)
    {
        $node = array();
        foreach ($layoutElement->attributes() as $attributeName => $attribute) {
            if ($attribute) {
                $node[$attributeName] = (string)$attribute;
            }
        }
        $node['type'] = self::TYPE;

        $parentNode['children'][] = & $node;
    }

    /**
     * @param array $meta
     * @param Layout $layout
     * @param array $parentNode
     */
    public function register(array & $meta, Layout $layout, array & $parentNode = null)
    {
        $elementName = isset($meta['element']) ? $meta['element'] : null;
        if (isset($elementName)) {
            $element = & $layout->getElement($elementName);
            if (isset($element) && isset($element['parent']['name'])) {
                $layout->unsetChild($element['parent']['name'], $elementName);

                $destinationName = isset($meta['destination']) ? $meta['destination'] : null;
                if (isset($destinationName)) {
                    $destination = & $layout->getElement($destinationName);
                    if ($destination) {
                        $alias = isset($meta['alias']) ? $meta['alias'] : null;
                        $before = isset($meta['before']) ? $meta['before'] : null;
                        $after = isset($meta['after']) ? $meta['after'] : null;
                        $element['alias'] = $alias;
                        $element['before'] = $before;
                        $element['after'] = $after;
                        $destination['children'][$element['name']] = $element;
                    }
                }
            }
        }
    }
}
