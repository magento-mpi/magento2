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
 * Widget Instance New page
 */
class WidgetInstanceNew extends BackendPage
{
    const MCA = 'admin/widget_instance/new/';

    protected $_blocks = [
        'form' => [
            'name' => 'form',
            'class' => 'Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Form',
            'locator' => '[id="page:main-container"]',
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
}
