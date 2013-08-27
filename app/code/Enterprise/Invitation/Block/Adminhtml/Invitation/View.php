<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Invitation view block
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 */
class Enterprise_Invitation_Block_Adminhtml_Invitation_View extends Magento_Adminhtml_Block_Widget_Container
{
    /**
     * Set header text, add some buttons
     *
     * @return Enterprise_Invitation_Block_Adminhtml_Invitation_View
     */
    protected function _prepareLayout()
    {
        $invitation = $this->getInvitation();
        $this->_headerText = __('View Invitation for %1 (ID: %2)', $invitation->getEmail(), $invitation->getId());
        $this->getLayout()->getBlock('page-title')->setPageTitle($this->_headerText);
        $this->_addButton('back', array(
            'label' => __('Back'),
            'onclick' => "setLocation('{$this->getUrl('*/*/')}')",
            'class' => 'back',
        ), -1);
        if ($invitation->canBeCanceled()) {
            $massCancelUrl = $this->getUrl('*/*/massCancel', array('_query' => array('invitations' => array($invitation->getId()))));
            $this->_addButton('cancel', array(
                'label' => __('Discard Invitation'),
                'onclick' => 'deleteConfirm(\''. $this->jsQuoteEscape(
                            __('Are you sure you want to discard this invitation?')
                        ) . '\', \'' . $massCancelUrl . '\' )',
                'class' => 'cancel'
            ), -1);
        }
        if ($invitation->canMessageBeUpdated()) {
            $this->_addButton('save_message_button', array(
                'label'   => __('Save Invitation'),
                'data_attribute'  => array(
                    'mage-init' => array(
                        'button' => array('event' => 'save', 'target' => '#invitation-elements'),
                    ),
                )
            ), -1);
        }
        if ($invitation->canBeSent()) {
            $massResendUrl = $this->getUrl('*/*/massResend', array('_query' => http_build_query(array('invitations' => array($invitation->getId())))));
            $this->_addButton('resend', array(
                'label' => __('Send Invitation'),
                'onclick' => "setLocation('{$massResendUrl}')",
            ), -1);
        }

        parent::_prepareLayout();
    }

    /**
     * Return Invitation for view
     *
     * @return Enterprise_Invitation_Model_Invitation
     */
    public function getInvitation()
    {
        return Mage::registry('current_invitation');
    }

    /**
     * Retrieve save message url
     *
     * @return string
     */
    public function getSaveMessageUrl()
    {
        return $this->getUrl('*/*/saveInvitation', array('id'=>$this->getInvitation()->getId()));
    }
}
