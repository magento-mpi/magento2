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
 * Block to display downloadable links bought by customer
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Downloadable\Block\Customer\Products;

class ListProducts extends \Magento\Core\Block\Template
{

    /**
     * Class constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $session = \Mage::getSingleton('Magento\Customer\Model\Session');
        $purchased = \Mage::getResourceModel('Magento\Downloadable\Model\Resource\Link\Purchased\Collection')
            ->addFieldToFilter('customer_id', $session->getCustomerId())
            ->addOrder('created_at', 'desc');
        $this->setPurchased($purchased);
        $purchasedIds = array();
        foreach ($purchased as $_item) {
            $purchasedIds[] = $_item->getId();
        }
        if (empty($purchasedIds)) {
            $purchasedIds = array(null);
        }
        $purchasedItems = \Mage::getResourceModel('Magento\Downloadable\Model\Resource\Link\Purchased\Item\Collection')
            ->addFieldToFilter('purchased_id', array('in' => $purchasedIds))
            ->addFieldToFilter('status',
                array(
                    'nin' => array(
                        \Magento\Downloadable\Model\Link\Purchased\Item::LINK_STATUS_PENDING_PAYMENT,
                        \Magento\Downloadable\Model\Link\Purchased\Item::LINK_STATUS_PAYMENT_REVIEW
                    )
                )
            )
            ->setOrder('item_id', 'desc');
        $this->setItems($purchasedItems);
    }

    /**
     * Enter description here...
     *
     * @return \Magento\Downloadable\Block\Customer\Products\ListProducts
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('Magento\Page\Block\Html\Pager', 'downloadable.customer.products.pager')
            ->setCollection($this->getItems());
        $this->setChild('pager', $pager);
        $this->getItems()->load();
        foreach ($this->getItems() as $item) {
            $item->setPurchased($this->getPurchased()->getItemById($item->getPurchasedId()));
        }
        return $this;
    }

    /**
     * Return order view url
     *
     * @param integer $orderId
     * @return string
     */
    public function getOrderViewUrl($orderId)
    {
        return $this->getUrl('sales/order/view', array('order_id' => $orderId));
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->getRefererUrl()) {
            return $this->getRefererUrl();
        }
        return $this->getUrl('customer/account/');
    }

    /**
     * Return number of left downloads or unlimited
     *
     * @return string
     */
    public function getRemainingDownloads($item)
    {
        if ($item->getNumberOfDownloadsBought()) {
            $downloads = $item->getNumberOfDownloadsBought() - $item->getNumberOfDownloadsUsed();
            return $downloads;
        }
        return __('Unlimited');
    }

    /**
     * Return url to download link
     *
     * @param \Magento\Downloadable\Model\Link\Purchased\Item $item
     * @return string
     */
    public function getDownloadUrl($item)
    {
        return $this->getUrl('*/download/link', array('id' => $item->getLinkHash(), '_secure' => true));
    }

    /**
     * Return true if target of link new window
     *
     * @return bool
     */
    public function getIsOpenInNewWindow()
    {
        return \Mage::getStoreConfigFlag(\Magento\Downloadable\Model\Link::XML_PATH_TARGET_NEW_WINDOW);
    }

}
