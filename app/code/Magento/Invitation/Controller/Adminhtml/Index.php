<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Invitation\Controller\Adminhtml;

use Magento\Backend\App\Action;

class Index extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Invitation Factory
     *
     * @var \Magento\Invitation\Model\InvitationFactory
     */
    protected $_invitationFactory;

    /**
     * Invitation Config
     *
     * @var \Magento\Invitation\Model\Config
     */
    protected $_config;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Invitation\Model\InvitationFactory $invitationFactory
     * @param \Magento\Invitation\Model\Config $config
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Invitation\Model\InvitationFactory $invitationFactory,
        \Magento\Invitation\Model\Config $config,
        \Magento\Framework\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        $this->_coreRegistry = $coreRegistry;
        $this->_invitationFactory = $invitationFactory;
        $this->_config = $config;
        parent::__construct($context);
    }

    /**
     * Init invitation model by request
     *
     * @return \Magento\Invitation\Model\Invitation
     * @throws \Magento\Framework\Model\Exception
     */
    protected function _initInvitation()
    {
        $this->_title->add(__('Invitations'));

        $invitation = $this->_invitationFactory->create()->load($this->getRequest()->getParam('id'));
        if (!$invitation->getId()) {
            throw new \Magento\Framework\Model\Exception(__("We couldn't find this invitation."));
        }
        $this->_coreRegistry->register('current_invitation', $invitation);

        return $invitation;
    }

    /**
     * Acl admin user check
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_config->isEnabled() && $this->_authorization->isAllowed(
            'Magento_Invitation::magento_invitation'
        );
    }
}
