<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\Handle\Reference;

use Magento\View\Layout\Handle\Render\Container as OriginalContainer;
use Magento\View\LayoutInterface;
use Magento\View\Layout\Element;

/**
 * @package Magento\View
 */
class Container extends OriginalContainer
{
    /**
     * Container type
     */
    const TYPE = 'referenceContainer';

    /**
     * @inheritdoc
     */
    public function parse(Element $layoutElement, LayoutInterface $layout, $parentName)
    {
        $this->parseReference($layoutElement, $layout, $parentName);

        return $this;
    }
}
