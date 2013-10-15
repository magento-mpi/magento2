<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\View\Element\Page;

use Magento\View\Element;
use Magento\View\Element\Container;
use Magento\View\Render\Html;

class Style extends Container implements Element
{
    /**
     * @param string $type
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function render($type = Html::TYPE_HTML)
    {
        return '';
    }
}
