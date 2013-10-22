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

interface RenderInterface extends HandleInterface
{
    /**
     * @param array $element
     * @param LayoutInterface $layout
     * @param string $parentName
     * @param string $type [optional]
     * @return string
     */
    public function render(array $element, LayoutInterface $layout, $parentName, $type = Html::TYPE_HTML);
}
