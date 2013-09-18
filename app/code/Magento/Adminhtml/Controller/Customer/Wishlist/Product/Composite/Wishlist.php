<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog composite product configuration controller
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Controller\Customer\Wishlist\Product\Composite;

class Wishlist
    extends \Magento\Adminhtml\Controller\Action
{
     /**
     * Wishlist we're working with
     *
     * @var \Magento\Wishlist\Model\Wishlist
     */
    protected $_wishlist = null;

    /**
     * Wishlist item we're working with
     *
     * @var \Magento\Wishlist\Model\Wishlist
     */
    protected $_wishlistItem = null;

    /**
     * Loads wishlist and wishlist item
     *
     * @return \Magento\Adminhtml\Controller\Customer\Wishlist\Product\Composite\Wishlist
     */
    protected function _initData()
    {
        $wishlistItemId = (int) $this->getRequest()->getParam('id');
        if (!$wishlistItemId) {
            \Mage::throwException(__('No wishlist item ID is defined.'));
        }

        /* @var $wishlistItem \Magento\Wishlist\Model\Item */
        $wishlistItem = \Mage::getModel('Magento\Wishlist\Model\Item')
            ->loadWithOptions($wishlistItemId);

        if (!$wishlistItem->getWishlistId()) {
            \Mage::throwException(__('Please load the wish list item.'));
        }

        $this->_wishlist = \Mage::getModel('Magento\Wishlist\Model\Wishlist')
            ->load($wishlistItem->getWishlistId());

        $this->_wishlistItem = $wishlistItem;

        return $this;
    }

    /**
     * Ajax handler to response configuration fieldset of composite product in customer's wishlist
     *
     * @return \Magento\Adminhtml\Controller\Customer\Wishlist\Product\Composite\Wishlist
     */
    public function configureAction()
    {
        $configureResult = new \Magento\Object();
        try {
            $this->_initData();

            $configureResult->setProductId($this->_wishlistItem->getProductId());
            $configureResult->setBuyRequest($this->_wishlistItem->getBuyRequest());
            $configureResult->setCurrentStoreId($this->_wishlistItem->getStoreId());
            $configureResult->setCurrentCustomerId($this->_wishlist->getCustomerId());

            $configureResult->setOk(true);
        } catch (\Exception $e) {
            $configureResult->setError(true);
            $configureResult->setMessage($e->getMessage());
        }

        $this->_objectManager->get('Magento\Adminhtml\Helper\Catalog\Product\Composite')
            ->renderConfigureResult($this, $configureResult);

        return $this;
    }

    /**
     * IFrame handler for submitted configuration for wishlist item
     *
     * @return false
     */
    public function updateAction()
    {
        // Update wishlist item
        $updateResult = new \Magento\Object();
        try {
            $this->_initData();

            $buyRequest = new \Magento\Object($this->getRequest()->getParams());

            $this->_wishlist
                ->updateItem($this->_wishlistItem->getId(), $buyRequest)
                ->save();

            $updateResult->setOk(true);
        } catch (\Exception $e) {
            $updateResult->setError(true);
            $updateResult->setMessage($e->getMessage());
        }
        $updateResult->setJsVarName($this->getRequest()->getParam('as_js_varname'));
        \Mage::getSingleton('Magento\Adminhtml\Model\Session')->setCompositeProductResult($updateResult);
        $this->_redirect('*/catalog_product/showUpdateResult');

        return false;
    }

    /**
     * Check the permission to Manage Customers
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Customer::manage');
    }
}
