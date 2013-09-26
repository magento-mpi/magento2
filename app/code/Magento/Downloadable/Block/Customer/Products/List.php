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
class Magento_Downloadable_Block_Customer_Products_List extends Magento_Core_Block_Template
{
    /**
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @var Magento_Downloadable_Model_Resource_Link_Purchased_CollectionFactory
     */
    protected $_linksFactory;

    /**
     * @var Magento_Downloadable_Model_Resource_Link_Purchased_Item_CollectionFactory
     */
    protected $_itemsFactory;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Downloadable_Model_Resource_Link_Purchased_CollectionFactory $linksFactory
     * @param Magento_Downloadable_Model_Resource_Link_Purchased_Item_CollectionFactory $itemsFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Customer_Model_Session $customerSession,
        Magento_Downloadable_Model_Resource_Link_Purchased_CollectionFactory $linksFactory,
        Magento_Downloadable_Model_Resource_Link_Purchased_Item_CollectionFactory $itemsFactory,
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
                        Magento_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PENDING_PAYMENT,
                        Magento_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PAYMENT_REVIEW
                    )
                )
            )
            ->setOrder('item_id', 'desc');
        $this->setItems($purchasedItems);
    }

    /**
     * Enter description here...
     *
     * @return Magento_Downloadable_Block_Customer_Products_List
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('Magento_Page_Block_Html_Pager', 'downloadable.customer.products.pager')
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
     * @param Magento_Downloadable_Model_Link_Purchased_Item $item
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
        return $this->_storeConfig->getConfigFlag(Magento_Downloadable_Model_Link::XML_PATH_TARGET_NEW_WINDOW);
    }

}
