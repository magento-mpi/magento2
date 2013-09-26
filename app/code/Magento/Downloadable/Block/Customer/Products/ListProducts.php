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
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Downloadable\Model\Resource\Link\Purchased\CollectionFactory
     */
    protected $_linksFactory;

    /**
     * @var \Magento\Downloadable\Model\Resource\Link\Purchased\Item\CollectionFactory
     */
    protected $_itemsFactory;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Downloadable\Model\Resource\Link\Purchased\CollectionFactory $linksFactory
     * @param \Magento\Downloadable\Model\Resource\Link\Purchased\Item\CollectionFactory $itemsFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Downloadable\Model\Resource\Link\Purchased\CollectionFactory $linksFactory,
        \Magento\Downloadable\Model\Resource\Link\Purchased\Item\CollectionFactory $itemsFactory,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        $this->_linksFactory = $linksFactory;
        $this->_itemsFactory = $itemsFactory;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Class constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $purchased = $this->_linksFactory->create()
            ->addFieldToFilter('customer_id', $this->_customerSession->getCustomerId())
            ->addOrder('created_at', 'desc');
        $this->setPurchased($purchased);
        $purchasedIds = array();
        foreach ($purchased as $_item) {
            $purchasedIds[] = $_item->getId();
        }
        if (empty($purchasedIds)) {
            $purchasedIds = array(null);
        }
        $purchasedItems = $this->_itemsFactory->create()
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
        return $this->_storeConfig->getConfigFlag(\Magento\Downloadable\Model\Link::XML_PATH_TARGET_NEW_WINDOW);
    }

}
