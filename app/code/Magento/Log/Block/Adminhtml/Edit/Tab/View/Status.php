<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Log\Block\Adminhtml\Edit\Tab\View;

class Status extends \Magento\Customer\Block\Adminhtml\Edit\Tab\View\PersonalInfo
{
    /**
     * Get customer last login date
     *
     * @return string
     */
    public function getLastLoginDate()
    {
        return __('Never');
    }

    /**
     * @return string
     */
    public function getStoreLastLoginDate()
    {
        return __('Never');
    }

    /**
     * @return string
     */
    public function getCurrentStatus()
    {
        return __('Offline');
    }
}
