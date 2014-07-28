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
 * Widget Instance Edit page
 */
class WidgetInstanceEdit extends BackendPage
{
    const MCA = 'admin/widget_instance/edit/code/magento_banner/';

    protected $_blocks = [
        'form' => [
            'name' => 'form',
            'class' => 'Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Form',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
        'bannerGrid' => [
            'name' => 'bannerGrid',
            'class' => 'Magento\Banner\Test\Block\Adminhtml\Banner\Grid',
            'locator' => '#bannerGrid',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Form
     */
    public function getForm()
    {
        return $this->getBlockInstance('form');
    }

    /**
     * @return \Magento\Banner\Test\Block\Adminhtml\Banner\Grid
     */
    public function getBannerGrid()
    {
        return $this->getBlockInstance('bannerGrid');
    }
}
