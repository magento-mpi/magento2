<?php
/**
 * Webhook permissions tab for integration activation dialog.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Block\Adminhtml\Integration\Activate\Permissions\Tabs;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Core\Block\Template;

class Webhook extends Template implements TabInterface
{
    /**
     * {@inheritDoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getTabLabel()
    {
        return __('Webhook');
    }

    /**
     * {@inheritDoc}
     */
    public function getTabTitle()
    {
        return __('Webhook');
    }

    /**
     * {@inheritDoc}
     */
    public function isHidden()
    {
        return false;
    }
}
