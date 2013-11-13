<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer account controller
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Downloadable\Controller;

use Magento\App\Action\NotFoundException;
use Magento\App\RequestInterface;

class Customer extends \Magento\App\Action\Action
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
     * Check customer authentication
     *
     * @param RequestInterface $request
     * @return mixed
     */
    public function dispatch(RequestInterface $request)
    {
        $loginUrl = $this->_objectManager->get('Magento\Customer\Helper\Data')->getLoginUrl();

        if (!$this->_customerSession->authenticate($this, $loginUrl)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
        return parent::dispatch($request);
    }

    /**
     * Display downloadable links bought by customer
     *
     */
    public function productsAction()
    {
        $this->_layoutServices->loadLayout();
        $this->_layoutServices->getLayout()->initMessages('Magento\Customer\Model\Session');
        if ($block = $this->_layoutServices->getLayout()->getBlock('downloadable_customer_products_list')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        $headBlock = $this->_layoutServices->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(__('My Downloadable Products'));
        }
        $this->_layoutServices->renderLayout();
    }

}
