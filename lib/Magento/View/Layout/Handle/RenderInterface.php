<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\View\Layout\Handle;

use Magento\View\LayoutInterface;
use Magento\View\Layout\HandleInterface;
use Magento\View\Render\Html;

/**
 * Render Interface
 *
 * @package Magento\View
 */
interface RenderInterface extends HandleInterface
{
    /**
     * @param string $elementName
     * @param LayoutInterface $layout
     * @return string
     */
    public function render($elementName, LayoutInterface $layout);
}
