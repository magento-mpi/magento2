<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\MultipleWishlist\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\MultipleWishlist\Controller\IndexInterface;

class Editwishlist extends \Magento\Framework\App\Action\Action implements IndexInterface
{
    /**
     * @var \Magento\MultipleWishlist\Model\WishlistEditor
     */
    protected $wishlistEditor;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @param Context $context
     * @param \Magento\MultipleWishlist\Model\WishlistEditor $wishlistEditor
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        Context $context,
        \Magento\MultipleWishlist\Model\WishlistEditor $wishlistEditor,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->wishlistEditor = $wishlistEditor;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * Edit wishlist properties
     *
     * @return \Magento\Framework\App\Action\Action|\Zend_Controller_Response_Abstract
     */
    public function execute()
    {
        $customerId = $this->customerSession->getCustomerId();
        $wishlistName = $this->getRequest()->getParam('name');
        $visibility = $this->getRequest()->getParam('visibility', 0) === 'on' ? 1 : 0;
        $wishlistId = $this->getRequest()->getParam('wishlist_id');
        $wishlist = null;
        try {
            $wishlist = $this->wishlistEditor->edit($customerId, $wishlistName, $visibility, $wishlistId);

            $this->messageManager->addSuccess(
                __(
                    'Wish List "%1" was saved.',
                    $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($wishlist->getName())
                )
            );
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong creating the wish list.'));
        }

        if (!$wishlist || !$wishlist->getId()) {
            $this->messageManager->addError('Could not create wishlist');
        }

        if ($this->getRequest()->isAjax()) {
            $params = [];
            if (!$wishlist->getId()) {
                $params = ['redirect' => $this->_url->getUrl('*/*')];
            } else {
                $params = ['wishlist_id' => $wishlist->getId()];
            }
            return $this->getResponse()->representJson(
                $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($params)
            );
        } else {
            if (!$wishlist || !$wishlist->getId()) {
                return $this->_redirect('*/*');
            } else {
                $this->_redirect('wishlist/index/index', ['wishlist_id' => $wishlist->getId()]);
            }
        }
    }
}
