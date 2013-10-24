<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\View\Layout\Handle;

use Magento\View\LayoutInterface;
use Magento\View\Layout\Element;
use Magento\View\Layout\HandleInterface;

class Arguments extends AbstractHandle implements HandleInterface
{
    /**
     * Handle type
     */
    const TYPE = 'arguments';

    /**
     * @inheritdoc
     *
     * @param Element $layoutElement
     * @param LayoutInterface $layout
     * @param string $parentName
     * @return Arguments
     */
    public function parse(Element $layoutElement, LayoutInterface $layout, $parentName)
    {
        $element = array();
        foreach ($layoutElement->attributes() as $attributeName => $attribute) {
            if ($attribute) {
                $element[$attributeName] = (string)$attribute;
            }
        }

        $arguments = $this->parseArguments($layoutElement);

        $layout->updateElement($parentName, array('arguments' => $arguments));

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @param array $element
     * @param LayoutInterface $layout
     * @param string $parentName
     */
    public function register(array $element, LayoutInterface $layout, $parentName)
    {
        //
    }
}
