<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sendfriend\Controller;

use Magento\Framework\App\Action\NotFoundException;
use Magento\Framework\App\RequestInterface;

/**
 * Email to a Friend Product Controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Product extends \Magento\Framework\App\Action\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Core\App\Action\FormKeyValidator
     */
    protected $_formKeyValidator;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Core\App\Action\FormKeyValidator $formKeyValidator
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Core\App\Action\FormKeyValidator $formKeyValidator
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_formKeyValidator = $formKeyValidator;
        parent::__construct($context);
    }

    /**
     * Check if module is enabled
     * If allow only for customer - redirect to login page
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     * @throws \Magento\Framework\App\Action\NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        /* @var $helper \Magento\Sendfriend\Helper\Data */
        $helper = $this->_objectManager->get('Magento\Sendfriend\Helper\Data');
        /* @var $session \Magento\Customer\Model\Session */
        $session = $this->_objectManager->get('Magento\Customer\Model\Session');

        if (!$helper->isEnabled()) {
            throw new NotFoundException();
        }

        if (!$helper->isAllowForGuest() && !$session->authenticate($this)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            if ($this->getRequest()->getActionName() == 'sendemail') {
                $session->setBeforeAuthUrl(
                    $this->_objectManager->create(
                        'Magento\Framework\UrlInterface'
                    )->getUrl(
                        '*/*/send',
                        array('_current' => true)
                    )
                );
                $this->_objectManager->get(
                    'Magento\Catalog\Model\Session'
                )->setSendfriendFormData(
                    $request->getPost()
                );
            }
        }
        return parent::dispatch($request);
    }

    /**
     * Initialize Product Instance
     *
     * @return \Magento\Catalog\Model\Product
     */
    protected function _initProduct()
    {
        $productId = (int)$this->getRequest()->getParam('id');
        if (!$productId) {
            return false;
        }
        $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($productId);
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            return false;
        }

        $this->_coreRegistry->register('product', $product);
        return $product;
    }

    /**
     * Initialize send friend model
     *
     * @return \Magento\Sendfriend\Model\Sendfriend
     */
    protected function _initSendToFriendModel()
    {
        /** @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress */
        $remoteAddress = $this->_objectManager->get('Magento\Framework\HTTP\PhpEnvironment\RemoteAddress');

        /** @var \Magento\Framework\Stdlib\CookieManager $cookieManager */
        $cookieManager = $this->_objectManager->get('Magento\Framework\Stdlib\CookieManager');

        /** @var \Magento\Store\Model\StoreManagerInterface $store */
        $store = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');

        /** @var \Magento\Sendfriend\Model\Sendfriend $model */
        $model = $this->_objectManager->create('Magento\Sendfriend\Model\Sendfriend');
        $model->setRemoteAddr($remoteAddress->getRemoteAddress(true));
        $model->setCookieManager($cookieManager);
        $model->setWebsiteId($store->getStore()->getWebsiteId());

        $this->_coreRegistry->register('send_to_friend_model', $model);

        return $model;
    }
}
