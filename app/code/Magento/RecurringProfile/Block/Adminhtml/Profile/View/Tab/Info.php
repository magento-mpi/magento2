<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Recurring profile information tab
 */
namespace Magento\RecurringProfile\Block\Adminhtml\Profile\View\Tab;

class Info
    extends \Magento\Backend\Block\Widget
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Label getter
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Profile Information');
    }

    /**
     * Also label getter :)
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getLabel();
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
