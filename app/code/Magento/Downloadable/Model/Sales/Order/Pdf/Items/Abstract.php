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
 * Order Downloadable Pdf Items renderer
 */
abstract class Magento_Downloadable_Model_Sales_Order_Pdf_Items_Abstract extends Magento_Sales_Model_Order_Pdf_Items_Abstract
{
    /**
     * Downloadable links purchased model
     *
     * @var Magento_Downloadable_Model_Link_Purchased
     */
    protected $_purchasedLinks = null;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * @var Magento_Downloadable_Model_Link_PurchasedFactory
     */
    protected $_purchasedFactory;

    /**
     * @var Magento_Downloadable_Model_Resource_Link_Purchased_Item_CollectionFactory
     */
    protected $_itemsFactory;

    /**
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Dir $coreDir
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Downloadable_Model_Link_PurchasedFactory $purchasedFactory
     * @param Magento_Downloadable_Model_Resource_Link_Purchased_Item_CollectionFactory $itemsFactory
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Tax_Helper_Data $taxData,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Dir $coreDir,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Downloadable_Model_Link_PurchasedFactory $purchasedFactory,
        Magento_Downloadable_Model_Resource_Link_Purchased_Item_CollectionFactory $itemsFactory,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_purchasedFactory = $purchasedFactory;
        $this->_itemsFactory = $itemsFactory;
        parent::__construct($taxData, $context, $registry, $coreDir, $resource, $resourceCollection, $data);
    }

    /**
     * Return Purchased link for order item
     *
     * @return Magento_Downloadable_Model_Link_Purchased
     */
    public function getLinks()
    {
        $this->_purchasedLinks = $this->_purchasedFactory->create()->load($this->getOrder()->getId(), 'order_id');
        $purchasedItems = $this->_itemsFactory->create()->addFieldToFilter(
            'order_item_id',
            $this->getItem()->getOrderItem()->getId()
        );
        $this->_purchasedLinks->setPurchasedItems($purchasedItems);

        return $this->_purchasedLinks;
    }

    /**
     * Return Links Section Title for order item
     *
     * @return string
     */
    public function getLinksTitle()
    {
        if ($this->_purchasedLinks->getLinkSectionTitle()) {
            return $this->_purchasedLinks->getLinkSectionTitle();
        }
        return $this->_coreStoreConfig->getConfig(Magento_Downloadable_Model_Link::XML_PATH_LINKS_TITLE);
    }
}
