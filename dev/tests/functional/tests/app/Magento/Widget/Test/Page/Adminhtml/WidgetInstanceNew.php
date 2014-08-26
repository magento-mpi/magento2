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
 * Class WidgetInstanceNew
 */
class WidgetInstanceNew extends BackendPage
{
    const MCA = 'admin/widget_instance/new/';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'widgetForm' => [
            'class' => 'Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\WidgetForm',
            'locator' => '[id="page:main-container"]',
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
}
