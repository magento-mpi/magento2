<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ProductAlert\Controller;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Action\Context;

class Add extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * Check customer authentication for some actions
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->_customerSession->authenticate($this)) {
            $this->_actionFlag->set('', 'no-dispatch', true);
            if (!$this->_customerSession->getBeforeUrl()) {
                $this->_customerSession->setBeforeUrl($this->_redirect->getRefererUrl());
            }
        }
        return parent::dispatch($request);
    }
}
