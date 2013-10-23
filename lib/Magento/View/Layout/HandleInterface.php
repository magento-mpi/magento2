<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\View\Layout;

use Magento\View\LayoutInterface;

/**
 * @package Magento\View
 */
interface HandleInterface
{
    /**
     * @param Element $layoutElement
     * @param LayoutInterface $layout
     * @param string $parentName
     */
    public function parse(Element $layoutElement, LayoutInterface $layout, $parentName);

    /**
     * @param array $element
     * @param LayoutInterface $layout
     * @param string $parentName
     * @return HandleInterface
     */
    public function register(array $element, LayoutInterface $layout, $parentName);
}
