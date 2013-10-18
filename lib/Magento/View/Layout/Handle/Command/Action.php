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

class Action implements Command
{
    /**
     * Container type
     */
    const TYPE = 'action';

    /**
     * @param Element $layoutElement
     * @param Layout $layout
     * @param array $parentNode
     */
    public function parse(Element $layoutElement, Layout $layout, array & $parentNode = array())
    {
        $node = array();
        foreach ($layoutElement->attributes() as $attributeName => $attribute) {
            if ($attribute) {
                $node[$attributeName] = (string)$attribute;
            }
        }
        $node['type'] = self::TYPE;

        $arguments = array();
        foreach ($layoutElement as $argument) {
            $name = $argument->getAttribute('name');
            $value = (string) $argument;
            $arguments[$name] = $value;
        }
        $node['arguments'] = $arguments;
        $node['parent_name'] = $parentNode['name'];
        $parentNode['children'][] = & $node;
    }

    /**
     * @param array $meta
     * @param Layout $layout
     * @param array $parentNode
     */
    public function register(array & $meta, Layout $layout, array & $parentNode = array())
    {
        $method = isset($meta['method']) ? $meta['method'] : null;
        if (isset($method) && isset($meta['parent']['_wrapped_'])) {
            $arguments = isset($meta['arguments']) ? $meta['arguments'] : array();
            call_user_func_array(array($meta['parent']['_wrapped_'], $method), $arguments);
        }
    }
}
