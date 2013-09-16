<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Recurring profile view data
 */
class Magento_Sales_Block_Recurring_Profile_View_Data extends Magento_Sales_Block_Recurring_Profile_View
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
            'reference_id' => $this->_profile->getReferenceId(),
            'can_cancel'   => $this->_profile->canCancel(),
            'cancel_url'   => $this->getUrl(
                '*/*/updateState',
                array(
                    'profile' => $this->_profile->getId(),
                    'action' => 'cancel'
                )
            ),
            'can_suspend'  => $this->_profile->canSuspend(),
            'suspend_url'  => $this->getUrl(
                '*/*/updateState',
                array(
                    'profile' => $this->_profile->getId(),
                    'action' => 'suspend'
                )
            ),
            'can_activate' => $this->_profile->canActivate(),
            'activate_url' => $this->getUrl(
                '*/*/updateState',
                array(
                    'profile' => $this->_profile->getId(),
                    'action' => 'activate'
                )
            ),
            'can_update'   => $this->_profile->canFetchUpdate(),
            'update_url'   => $this->getUrl(
                '*/*/updateProfile',
                array(
                    'profile' => $this->_profile->getId()
                )
            ),
            'back_url'     => $this->getUrl('*/*/'),
            'confirmation_message' => __('Are you sure you want to do this?'),
        ));
    }
}
