<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ProductAlert
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ProductAlert\Controller;

/**
 * ProductAlert unsubscribe controller
 *
 * @category   Magento
 * @package    Magento_ProductAlert
 * @author      Magento Core Team <core@magentocommerce.com>
 */
use Magento\Framework\App\Action\NotFoundException;
use Magento\Framework\App\RequestInterface;

class Unsubscribe extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\App\Action\Context $context,
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

    /**
     * @return void
     */
    public function priceAction()
    {
        $productId = (int)$this->getRequest()->getParam('product');

        if (!$productId) {
            $this->_redirect('');
            return;
        }
        $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($productId);
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            /* @var $product \Magento\Catalog\Model\Product */
            $this->messageManager->addError(__('We can\'t find the product.'));
            $this->_redirect('customer/account/');
            return;
        }

        try {
            $model = $this->_objectManager->create(
                'Magento\ProductAlert\Model\Price'
            )->setCustomerId(
                $this->_customerSession->getCustomerId()
            )->setProductId(
                $product->getId()
            )->setWebsiteId(
                $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getWebsiteId()
            )->loadByParam();
            if ($model->getId()) {
                $model->delete();
            }

            $this->messageManager->addSuccess(__('You deleted the alert subscription.'));
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Unable to update the alert subscription.'));
        }
        $this->getResponse()->setRedirect($product->getProductUrl());
    }

    /**
     * @return void
     */
    public function priceAllAction()
    {
        try {
            $this->_objectManager->create(
                'Magento\ProductAlert\Model\Price'
            )->deleteCustomer(
                $this->_customerSession->getCustomerId(),
                $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getWebsiteId()
            );
            $this->messageManager->addSuccess(__('You will no longer receive price alerts for this product.'));
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Unable to update the alert subscription.'));
        }
        $this->_redirect('customer/account/');
    }

    /**
     * @return void
     */
    public function stockAction()
    {
        $productId = (int)$this->getRequest()->getParam('product');

        if (!$productId) {
            $this->_redirect('');
            return;
        }

        /* @var $product \Magento\Catalog\Model\Product */
        $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($productId);
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            $this->messageManager->addError(__('The product was not found.'));
            $this->_redirect('customer/account/');
            return;
        }

        try {
            $model = $this->_objectManager->create(
                'Magento\ProductAlert\Model\Stock'
            )->setCustomerId(
                $this->_customerSession->getCustomerId()
            )->setProductId(
                $product->getId()
            )->setWebsiteId(
                $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getWebsiteId()
            )->loadByParam();
            if ($model->getId()) {
                $model->delete();
            }
            $this->messageManager->addSuccess(__('You will no longer receive stock alerts for this product.'));
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Unable to update the alert subscription.'));
        }
        $this->getResponse()->setRedirect($product->getProductUrl());
    }

    /**
     * @return void
     */
    public function stockAllAction()
    {
        try {
            $this->_objectManager->create(
                'Magento\ProductAlert\Model\Stock'
            )->deleteCustomer(
                $this->_customerSession->getCustomerId(),
                $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getWebsiteId()
            );
            $this->messageManager->addSuccess(__('You will no longer receive stock alerts.'));
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Unable to update the alert subscription.'));
        }
        $this->_redirect('customer/account/');
    }
}
