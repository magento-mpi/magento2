<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Invitation\Model;

class InvitationProvider
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var InvitationFactory
     */
    protected $invitationFactory;

    /**
     * @var \Magento\Core\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\Framework\Registry $registry
     * @param InvitationFactory $invitationFactory
     * @param \Magento\Core\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        InvitationFactory $invitationFactory,
        \Magento\Core\Helper\Data $helper
    ) {
        $this->registry = $registry;
        $this->invitationFactory = $invitationFactory;
        $this->helper = $helper;
    }

    /**
     * @return \Magento\Invitation\Model\Invitation
     */
    public function get()
    {
        if (!$this->registry->registry('current_invitation')) {
            $invitation = $this->invitationFactory->create();
            $invitation->loadByInvitationCode(
                $this->helper->urlDecode(
                    $this->getRequest()->getParam('invitation', false)
                )
            )->makeSureCanBeAccepted();
            $this->registry->register('current_invitation', $invitation);
        }
        return $this->registry->registry('current_invitation');
    }
}
