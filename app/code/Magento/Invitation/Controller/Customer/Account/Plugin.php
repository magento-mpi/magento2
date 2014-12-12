<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Invitation\Controller\Customer\Account;

use Magento\Framework\App\Action\NotFoundException;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;

class Plugin
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Invitation\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $redirector;

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Invitation\Model\Config $config
     * @param \Magento\Framework\App\Response\RedirectInterface $redirector
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Invitation\Model\Config $config,
        \Magento\Framework\App\Response\RedirectInterface $redirector
    ) {
        $this->session = $customerSession;
        $this->config = $config;
        $this->redirector = $redirector;
    }

    /**
     * Check if invitation is enabled
     *
     * @param ActionInterface $subject
     * @param RequestInterface $request
     * @return void
     * @throws \Magento\Framework\App\Action\NotFoundException
     */
    public function beforeDispatch(ActionInterface $subject, RequestInterface $request)
    {
        if (!$this->config->isEnabledOnFront()) {
            throw new NotFoundException();
        }
        if ($this->session->isLoggedIn()) {
            $this->redirector->redirect($subject->getResponse(), 'customer/account/');
            $subject->getActionFlag()->set('', $subject::FLAG_NO_DISPATCH, true);
        }
    }
}
