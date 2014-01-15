<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Invitation view block
 *
 * @category   Magento
 * @package    Magento_Invitation
 */
namespace Magento\Invitation\Block\Adminhtml\Invitation;

class View extends \Magento\Backend\Block\Widget\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Set header text, add some buttons
     *
     * @return \Magento\Invitation\Block\Adminhtml\Invitation\View
     */
    protected function _prepareLayout()
    {
        $invitation = $this->getInvitation();
        $this->_headerText = __('View Invitation for %1 (ID: %2)', $invitation->getEmail(), $invitation->getId());
        $this->getLayout()->getBlock('page-title')->setPageTitle($this->_headerText);
        $this->_addButton('back', array(
            'label' => __('Back'),
            'onclick' => "setLocation('{$this->getUrl('invitations/*/')}')",
            'class' => 'back',
        ), -1);
        if ($invitation->canBeCanceled()) {
            $massCancelUrl = $this->getUrl('invitations/*/massCancel', array('_query' => array('invitations' => array($invitation->getId()))));
            $this->_addButton('cancel', array(
                'label' => __('Discard Invitation'),
                'onclick' => 'deleteConfirm(\''. $this->escapeJsQuote(
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
            $massResendUrl = $this->getUrl('invitations/*/massResend', array('_query' => http_build_query(array('invitations' => array($invitation->getId())))));
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
     * @return \Magento\Invitation\Model\Invitation
     */
    public function getInvitation()
    {
        return $this->_coreRegistry->registry('current_invitation');
    }

    /**
     * Retrieve save message url
     *
     * @return string
     */
    public function getSaveMessageUrl()
    {
        return $this->getUrl('invitations/*/saveInvitation', array('id'=>$this->getInvitation()->getId()));
    }
}
