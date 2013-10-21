<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\View\Layout;

//use Magento\View\Context;
use Magento\View\Layout;
use Magento\View\Layout\Element;

interface Handle
{
    /**
     * @param Element $layoutElement
     * @param Layout $layout
     * @param string $parentName
     */
    public function parse(Element $layoutElement, Layout $layout, $parentName);

    /**
     * @param array $element
     * @param Layout $layout
     * @param string $parentName
     * @return Handle
     */
    public function register(array $element, Layout $layout, $parentName);
}
