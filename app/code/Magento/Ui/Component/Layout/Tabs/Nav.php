<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Component\Layout\Tabs;

use Magento\Ui\Component\AbstractView;
use Magento\Framework\View\Element\Template;
use Magento\Ui\DataProvider\Metadata;

/**
 * Class Nav
 */
class Nav extends AbstractView
{
    /**
     * @return array
     */
    public function getTabs()
    {
        $tabs = $this->getLayoutElement('tabs', []);
        return isset($tabs['children']) ? $tabs['children'] : [];
    }
}
