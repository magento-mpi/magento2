<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\View\Layout\Handle;

use Magento\View\Context;
use Magento\View\Layout;
use Magento\View\Layout\Handle;
use Magento\View\Render\Html;

interface Render extends Handle
{
    /**
     * @param array $meta
     * @param Layout $layout
     * @param array $parentNode
     * @param $type
     * @return mixed
     */
    public function render(array & $meta, Layout $layout, array & $parentNode = array(), $type = Html::TYPE_HTML);
}
