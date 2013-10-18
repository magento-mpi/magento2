<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\View\Layout;

use Magento\View\Context;
use Magento\View\Layout;
use Magento\View\Layout\Element;

interface Handle
{
    /**
     * @param Element $layoutElement
     * @param Layout $layout
     * @param array $parentNode
     */
    public function parse(Element $layoutElement, Layout $layout, array & $parentNode = array());

    /**
     * @param array $meta
     * @param Layout $layout
     * @param array $parentNode
     */
    public function register(array & $meta, Layout $layout, array & $parentNode = array());
}
