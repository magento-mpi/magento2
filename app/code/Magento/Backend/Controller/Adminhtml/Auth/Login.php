<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\Auth;

class Login extends \Magento\Backend\Controller\Adminhtml\Auth
{
    /**
     * @var \Magento\Backend\Model\View\Result\PageFactory
     */
    protected $pageFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\RedirectFactory
     */
    protected $redirectFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Backend\Model\View\Result\PageFactory $pageFactory
     * @param \Magento\Backend\Model\View\Result\RedirectFactory $redirectFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\View\Result\PageFactory $pageFactory,
        \Magento\Backend\Model\View\Result\RedirectFactory $redirectFactory
    ) {
        $this->pageFactory = $pageFactory;
        $this->redirectFactory = $redirectFactory;
        parent::__construct($context);
    }

    /**
     * Administrator login action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        if ($this->_auth->isLoggedIn()) {
            if ($this->_auth->getAuthStorage()->isFirstPageAfterLogin()) {
                $this->_auth->getAuthStorage()->setIsFirstPageAfterLogin(true);
            }
            $redirectResult = $this->redirectFactory->create();
            $redirectResult->setUrl($this->_backendUrl->getStartupPageUrl());
            return $redirectResult;
        }
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }
}
