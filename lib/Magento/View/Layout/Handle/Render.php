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
    public function render(array & $meta, Layout $layout, array & $parentNode = null, $type = Html::TYPE_HTML);
}
