<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\RecurringProfile\Block\Profile\View;

/**
 * Recurring profile view data
 */
class Data extends \Magento\RecurringProfile\Block\Profile\View
{
    /**
     * Prepare profile data
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->addData(array(
            'reference_id' => $this->_recurringProfile->getReferenceId(),
            'can_cancel'   => $this->_recurringProfile->canCancel(),
            'cancel_url'   => $this->getUrl(
                '*/*/updateState',
                array(
                    'profile' => $this->_recurringProfile->getId(),
                    'action' => 'cancel'
                )
            ),
            'can_suspend'  => $this->_recurringProfile->canSuspend(),
            'suspend_url'  => $this->getUrl(
                '*/*/updateState',
                array(
                    'profile' => $this->_recurringProfile->getId(),
                    'action' => 'suspend'
                )
            ),
            'can_activate' => $this->_recurringProfile->canActivate(),
            'activate_url' => $this->getUrl(
                '*/*/updateState',
                array(
                    'profile' => $this->_recurringProfile->getId(),
                    'action' => 'activate'
                )
            ),
            'can_update'   => $this->_recurringProfile->canFetchUpdate(),
            'update_url'   => $this->getUrl(
                '*/*/updateProfile',
                array(
                    'profile' => $this->_recurringProfile->getId()
                )
            ),
            'back_url'     => $this->getUrl('*/*/'),
            'confirmation_message' => __('Are you sure you want to do this?'),
        ));
    }
}
