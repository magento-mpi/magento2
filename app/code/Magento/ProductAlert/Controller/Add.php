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

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;

/**
 * ProductAlert controller
 *
 * @category   Magento
 * @package    Magento_ProductAlert
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Add extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
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
        if (!$this->_objectManager->get('Magento\Customer\Model\Session')->authenticate($this)) {
            $this->_actionFlag->set('', 'no-dispatch', true);
            if (!$this->_objectManager->get('Magento\Customer\Model\Session')->getBeforeUrl()) {
                $this->_objectManager->get(
                    'Magento\Customer\Model\Session'
                )->setBeforeUrl(
                    $this->_redirect->getRefererUrl()
                );
            }
        }
        return parent::dispatch($request);
    }

    /**
     * @return void
     */
    public function testObserverAction()
    {
        $object = new \Magento\Object();
        $observer = $this->_objectManager->get('Magento\ProductAlert\Model\Observer');
        $observer->process($object);
    }

    /**
     * @return void
     */
    public function priceAction()
    {
        $backUrl = $this->getRequest()->getParam(\Magento\Framework\App\Action\Action::PARAM_NAME_URL_ENCODED);
        $productId = (int)$this->getRequest()->getParam('product_id');
        if (!$backUrl || !$productId) {
            $this->_redirect('/');
            return;
        }

        $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($productId);
        if (!$product->getId()) {
            /* @var $product \Magento\Catalog\Model\Product */
            $this->messageManager->addError(__('There are not enough parameters.'));
            if ($this->_isInternal($backUrl)) {
                $this->getResponse()->setRedirect($backUrl);
            } else {
                $this->_redirect('/');
            }
            return;
        }

        try {
            $model = $this->_objectManager->create(
                'Magento\ProductAlert\Model\Price'
            )->setCustomerId(
                $this->_objectManager->get('Magento\Customer\Model\Session')->getId()
            )->setProductId(
                $product->getId()
            )->setPrice(
                $product->getFinalPrice()
            )->setWebsiteId(
                $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getWebsiteId()
            );
            $model->save();
            $this->messageManager->addSuccess(__('You saved the alert subscription.'));
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Unable to update the alert subscription.'));
        }
        $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl());
    }

    /**
     * @return void
     */
    public function stockAction()
    {
        $backUrl = $this->getRequest()->getParam(\Magento\Framework\App\Action\Action::PARAM_NAME_URL_ENCODED);
        $productId = (int)$this->getRequest()->getParam('product_id');
        if (!$backUrl || !$productId) {
            $this->_redirect('/');
            return;
        }

        if (!($product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($productId))) {
            /* @var $product \Magento\Catalog\Model\Product */
            $this->messageManager->addError(__('There are not enough parameters.'));
            $this->getResponse()->setRedirect($backUrl);
            return;
        }

        try {
            $model = $this->_objectManager->create(
                'Magento\ProductAlert\Model\Stock'
            )->setCustomerId(
                $this->_objectManager->get('Magento\Customer\Model\Session')->getId()
            )->setProductId(
                $product->getId()
            )->setWebsiteId(
                $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getWebsiteId()
            );
            $model->save();
            $this->messageManager->addSuccess(__('Alert subscription has been saved.'));
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Unable to update the alert subscription.'));
        }
        $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl());
    }

    /**
     * Check if URL is internal
     *
     * @param string $url
     * @return bool
     */
    protected function _isInternal($url)
    {
        if (strpos($url, 'http') === false) {
            return false;
        }
        $currentStore = $this->_storeManager->getStore();
        return strpos(
            $url,
            $currentStore->getBaseUrl()
        ) === 0 || strpos(
            $url,
            $currentStore->getBaseUrl(\Magento\UrlInterface::URL_TYPE_LINK, true)
        ) === 0;
    }
}
