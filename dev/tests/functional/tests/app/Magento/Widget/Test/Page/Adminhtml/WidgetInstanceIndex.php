<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class WidgetInstanceIndex
 * Widget Instance Index page
 */
class WidgetInstanceIndex extends BackendPage
{
    const MCA = 'admin/widget_instance/index';

    protected $_blocks = [
        'pageActionsBlock' => [
            'name' => 'pageActionsBlock',
            'class' => 'Magento\Backend\Test\Block\GridPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'widgetGrid' => [
            'name' => 'widgetGrid',
            'class' => 'Magento\Widget\Test\Block\Adminhtml\Widget\WidgetGrid',
            'locator' => '#widgetInstanceGrid',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Backend\Test\Block\GridPageActions
     */
    public function getPageActionsBlock()
    {
        return $this->getBlockInstance('pageActionsBlock');
    }

    /**
     * @return \Magento\Widget\Test\Block\Adminhtml\Widget\WidgetGrid
     */
    public function getWidgetGrid()
    {
        return $this->getBlockInstance('widgetGrid');
    }
}
