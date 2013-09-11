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
 *
 * @category   Magento
 * @package    Magento_Downloadable
 * @author     Magento Core Team <core@magentocommerce.com>
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
    protected $_coreStoreConfig = null;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Return Purchased link for order item
     *
     * @return Magento_Downloadable_Model_Link_Purchased
     */
    public function getLinks()
    {
        $this->_purchasedLinks = Mage::getModel('Magento_Downloadable_Model_Link_Purchased')
            ->load($this->getOrder()->getId(), 'order_id');
        $purchasedItems = Mage::getModel('Magento_Downloadable_Model_Link_Purchased_Item')->getCollection()
            ->addFieldToFilter('order_item_id', $this->getItem()->getOrderItem()->getId());
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
