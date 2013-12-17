<?php
/**
 * Permissions tab for integration activation dialog.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Block\Adminhtml\Integration\Activate\Permissions;

use Magento\Backend\Block\Widget\Tabs as TabsWidget;

class Tabs extends TabsWidget
{
    protected $_template = 'Magento_Backend::widget/tabshoriz.phtml';

    protected function _construct()
    {
        parent::_construct();
        $this->setDestElementId('integrations-activate-permissions-content');
    }
}
