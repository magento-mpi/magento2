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
 * Class WidgetInstanceEdit
 *
 * @package Magento\Widget\Test\Page\Adminhtml
 */
class WidgetInstanceEdit extends BackendPage
{
    const MCA = 'admin/widget_instance/edit/code/magento_banner/';

    protected $_blocks = [
        'widgetForm' => [
            'name' => 'widgetForm',
            'class' => 'Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\WidgetForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
        'bannerGrid' => [
            'name' => 'bannerGrid',
            'class' => 'Magento\Banner\Test\Block\Adminhtml\Banner\Grid',
            'locator' => '#bannerGrid',
            'strategy' => 'css selector',
        ],
        'pageActionsBlock' => [
            'name' => 'pageActionsBlock',
            'class' => 'Magento\Backend\Test\Block\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\WidgetForm
     */
    public function getWidgetForm()
    {
        return $this->getBlockInstance('widgetForm');
    }

    /**
     * @return \Magento\Banner\Test\Block\Adminhtml\Banner\Grid
     */
    public function getBannerGrid()
    {
        return $this->getBlockInstance('bannerGrid');
    }

    /**
     * @return \Magento\Backend\Test\Block\FormPageActions
     */
    public function getPageActionsBlock()
    {
        return $this->getBlockInstance('pageActionsBlock');
    }
}
