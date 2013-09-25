<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Product Mass Action processing model
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Product_Action extends Magento_Core_Model_Abstract
{
    /**
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager = null;

    /**
     * Index indexer
     *
     * @var Magento_Index_Model_Indexer
     */
    protected $_indexIndexer;

    /**
     * Product website factory
     *
     * @var Magento_Catalog_Model_Product_WebsiteFactory
     */
    protected $_productWebsiteFactory;

    /**
     * Construct
     *
     * @param Magento_Catalog_Model_Product_WebsiteFactory $productWebsiteFactory
     * @param Magento_Index_Model_Indexer $indexIndexer
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Model_Product_WebsiteFactory $productWebsiteFactory,
        Magento_Index_Model_Indexer $indexIndexer,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_productWebsiteFactory = $productWebsiteFactory;
        $this->_indexIndexer = $indexIndexer;
        $this->_eventManager = $eventManager;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Catalog_Model_Resource_Product_Action');
    }

    /**
     * Retrieve resource instance wrapper
     *
     * @return Magento_Catalog_Model_Resource_Product_Action
     */
    protected function _getResource()
    {
        return parent::_getResource();
    }

    /**
     * Update attribute values for entity list per store
     *
     * @param array $productIds
     * @param array $attrData
     * @param int $storeId
     * @return Magento_Catalog_Model_Product_Action
     */
    public function updateAttributes($productIds, $attrData, $storeId)
    {
        $this->_eventManager->dispatch('catalog_product_attribute_update_before', array(
            'attributes_data' => &$attrData,
            'product_ids'   => &$productIds,
            'store_id'      => &$storeId
        ));

        $this->_getResource()->updateAttributes($productIds, $attrData, $storeId);
        $this->setData(array(
            'product_ids'       => array_unique($productIds),
            'attributes_data'   => $attrData,
            'store_id'          => $storeId
        ));

        // register mass action indexer event
        $this->_indexIndexer->processEntityAction(
            $this, Magento_Catalog_Model_Product::ENTITY, Magento_Index_Model_Event::TYPE_MASS_ACTION
        );
        return $this;
    }

    /**
     * Update websites for product action
     *
     * allowed types:
     * - add
     * - remove
     *
     * @param array $productIds
     * @param array $websiteIds
     * @param string $type
     */
    public function updateWebsites($productIds, $websiteIds, $type)
    {
        if ($type == 'add') {
            $this->_productWebsiteFactory->create()->addProducts($websiteIds, $productIds);
        } else if ($type == 'remove') {
            $this->_productWebsiteFactory->create()->removeProducts($websiteIds, $productIds);
        }

        $this->setData(array(
            'product_ids' => array_unique($productIds),
            'website_ids' => $websiteIds,
            'action_type' => $type
        ));

        // register mass action indexer event
        $this->_indexIndexer->processEntityAction(
            $this, Magento_Catalog_Model_Product::ENTITY, Magento_Index_Model_Event::TYPE_MASS_ACTION
        );
    }
}
