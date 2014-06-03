<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Controller;

use Magento\Framework\App\RequestInterface;

/**
 * Customer account controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Customer extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Customer\Model\Session $customerSession)
    {
        $this->_customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * Check customer authentication
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        $loginUrl = $this->_objectManager->get('Magento\Customer\Helper\Data')->getLoginUrl();

        if (!$this->_customerSession->authenticate($this, $loginUrl)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }
        return parent::dispatch($request);
    }

    /**
     * Display downloadable links bought by customer
     *
     * @return void
     */
    public function productsAction()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        if ($block = $this->_view->getLayout()->getBlock('downloadable_customer_products_list')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        $headBlock = $this->_view->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(__('My Downloadable Products'));
        }
        $this->_view->renderLayout();
    }
}
