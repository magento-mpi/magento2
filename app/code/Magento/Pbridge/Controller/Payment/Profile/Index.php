<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Pbridge\Controller\Payment\Profile;

use Magento\Framework\App\RequestInterface;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Construct
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Customer\Model\Session $customerSession)
    {
        $this->_customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * Check whether Payment Profiles functionality enabled
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->_objectManager->get('Magento\Pbridge\Helper\Data')->arePaymentProfilesEnables()) {
            if ($request->getActionName() != 'noroute') {
                $this->_forward('noroute');
            }
        }
        return parent::dispatch($request);
    }

    /**
     * Payment Bridge frame with Saved Payment profiles
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->_customerSession->getCustomerId()) {
            $this->_customerSession->authenticate($this);
            return;
        }

        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
