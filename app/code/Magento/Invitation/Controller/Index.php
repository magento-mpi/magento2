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
 * Invitation frontend controller
 *
 * @category   Magento
 * @package    Magento_Invitation
 */
namespace Magento\Invitation\Controller;

use Magento\App\Action\NotFoundException;
use Magento\App\RequestInterface;

class Index extends \Magento\App\Action\Action
{
    /**
     * Customer Session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_session;

    /**
     * Invitation Config
     *
     * @var \Magento\Invitation\Model\Config
     */
    protected $_config;

    /**
     * Invitation Factory
     *
     * @var \Magento\Invitation\Model\InvitationFactory
     */
    protected $invitationFactory;

    /**
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Invitation\Model\Config $config
     * @param \Magento\Invitation\Model\InvitationFactory $invitationFactory
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Customer\Model\Session $session,
        \Magento\Invitation\Model\Config $config,
        \Magento\Invitation\Model\InvitationFactory $invitationFactory
    ) {
        parent::__construct($context);
        $this->_session = $session;
        $this->_config = $config;
        $this->invitationFactory = $invitationFactory;
    }

    /**
     * Only logged in users can use this functionality,
     * this function checks if user is logged in before all other actions
     *
     * @param RequestInterface $request
     * @return \Magento\App\ResponseInterface
     * @throws \Magento\App\Action\NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->_config->isEnabledOnFront()) {
            throw new NotFoundException();
        }

        if (!$this->_session->authenticate($this)) {
            $this->getResponse()->setRedirect(
                $this->_objectManager->get('Magento\Customer\Helper\Data')->getLoginUrl()
            );
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }
        return parent::dispatch($request);
    }

    /**
     * Send invitations from frontend
     *
     * @return void
     */
    public function sendAction()
    {
        $data = $this->getRequest()->getPost();
        if ($data) {
            $customer = $this->_session->getCustomer();
            $message = isset($data['message']) ? $data['message'] : '';
            if (!$this->_config->isInvitationMessageAllowed()) {
                $message = '';
            }
            $invPerSend = $this->_config->getMaxInvitationsPerSend();
            $attempts = 0;
            $sent     = 0;
            $customerExists = 0;
            foreach ($data['email'] as $email) {
                $attempts++;
                if (!\Zend_Validate::is($email, 'EmailAddress')) {
                    continue;
                }
                if ($attempts > $invPerSend) {
                    continue;
                }
                try {
                    $invitation = $this->invitationFactory->create()->setData(array(
                        'email'    => $email,
                        'customer' => $customer,
                        'message'  => $message
                    ))->save();
                    if ($invitation->sendInvitationEmail()) {
                        $this->messageManager->addSuccess(__('You sent the invitation for %1.', $email));
                        $sent++;
                    } else {
                        throw new \Exception(''); // not \Magento\Core\Exception intentionally
                    }
                } catch (\Magento\Core\Exception $e) {
                    if (\Magento\Invitation\Model\Invitation::ERROR_CUSTOMER_EXISTS === $e->getCode()) {
                        $customerExists++;
                    } else {
                        $this->messageManager->addError($e->getMessage());
                    }
                } catch (\Exception $e) {
                    $this->messageManager->addError(__('Something went wrong sending an email to %1.', $email));
                }
            }
            if ($customerExists) {
                $this->messageManager->addNotice(
                    __('We did not send %1 invitation(s) addressed to current customers.', $customerExists)
                );
            }
            $this->_redirect('*/*/');
            return;
        }

        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->loadLayoutUpdates();
        $headBlock = $this->_view->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(__('Send Invitations'));
        }
        $this->_view->renderLayout();
    }

    /**
     * View invitation list in 'My Account' section
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->loadLayoutUpdates();
        if ($block = $this->_view->getLayout()->getBlock('invitations_list')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        $headBlock = $this->_view->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(__('My Invitations'));
        }
        $this->_view->renderLayout();
    }
}
