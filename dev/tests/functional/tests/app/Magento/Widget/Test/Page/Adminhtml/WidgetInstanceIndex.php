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
 */
class WidgetInstanceIndex extends BackendPage
{
    const MCA = 'admin/widget_instance/index';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'pageActionsBlock' => [
            'class' => 'Magento\Backend\Test\Block\GridPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'widgetGrid' => [
            'class' => 'Magento\Backend\Test\Block\Widget\Grid',
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
     * @return \Magento\Backend\Test\Block\Widget\Grid
     */
    public function getWidgetGrid()
    {
        return $this->getBlockInstance('widgetGrid');
    }
}
