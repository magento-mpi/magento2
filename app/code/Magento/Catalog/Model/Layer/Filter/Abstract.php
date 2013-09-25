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
 * Layer category filter abstract model
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Catalog_Model_Layer_Filter_Abstract extends Magento_Object
{
    /**
     * Request variable name with filter value
     *
     * @var string
     */
    protected $_requestVar;

    /**
     * Array of filter items
     *
     * @var array
     */
    protected $_items;

    /**
     * Catalog layer
     *
     * @var Magento_Catalog_Model_Layer
     */
    protected $_catalogLayer;

    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Filter item factory
     *
     * @var Magento_Catalog_Model_Layer_Filter_ItemFactory
     */
    protected $_filterItemFactory;

    /**
     * Constructor
     *
     * @param Magento_Catalog_Model_Layer_Filter_ItemFactory $filterItemFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Catalog_Model_Layer $catalogLayer
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Model_Layer_Filter_ItemFactory $filterItemFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Catalog_Model_Layer $catalogLayer,
        array $data = array()
    ) {
        $this->_filterItemFactory = $filterItemFactory;
        $this->_storeManager = $storeManager;
        $this->_catalogLayer = $catalogLayer;
        parent::__construct($data);
    }

    /**
     * Set request variable name which is used for apply filter
     *
     * @param   string $varName
     * @return  Magento_Catalog_Model_Layer_Filter_Abstract
     */
    public function setRequestVar($varName)
    {
        $this->_requestVar = $varName;
        return $this;
    }

    /**
     * Get request variable name which is used for apply filter
     *
     * @return string
     */
    public function getRequestVar()
    {
        return $this->_requestVar;
    }

    /**
     * Get filter value for reset current filter state
     *
     * @return mixed
     */
    public function getResetValue()
    {
        return null;
    }

    /**
     * Retrieve filter value for Clear All Items filter state
     *
     * @return mixed
     */
    public function getCleanValue()
    {
        return null;
    }

    /**
     * Apply filter to collection
     *
     * @param  Zend_Controller_Request_Abstract $request
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        return $this;
    }

    /**
     * Get fiter items count
     *
     * @return int
     */
    public function getItemsCount()
    {
        return count($this->getItems());
    }

    /**
     * Get all filter items
     *
     * @return array
     */
    public function getItems()
    {
        if (is_null($this->_items)) {
            $this->_initItems();
        }
        return $this->_items;
    }

    /**
     * Get data array for building filter items
     *
     * result array should have next structure:
     * array(
     *      $index => array(
     *          'label' => $label,
     *          'value' => $value,
     *          'count' => $count
     *      )
     * )
     *
     * @return array
     */
    protected function _getItemsData()
    {
        return array();
    }

    /**
     * Initialize filter items
     *
     * @return  Magento_Catalog_Model_Layer_Filter_Abstract
     */
    protected function _initItems()
    {
        $data = $this->_getItemsData();
        $items=array();
        foreach ($data as $itemData) {
            $items[] = $this->_createItem(
                $itemData['label'],
                $itemData['value'],
                $itemData['count']
            );
        }
        $this->_items = $items;
        return $this;
    }


    /**
     * Retrieve layer object
     *
     * @return Magento_Catalog_Model_Layer
     */
    public function getLayer()
    {
        $layer = $this->_getData('layer');
        if (is_null($layer)) {
            $layer = $this->_catalogLayer;
            $this->setData('layer', $layer);
        }
        return $layer;
    }

    /**
     * Create filter item object
     *
     * @param   string $label
     * @param   mixed $value
     * @param   int $count
     * @return  Magento_Catalog_Model_Layer_Filter_Item
     */
    protected function _createItem($label, $value, $count=0)
    {
        return $this->_filterItemFactory->create()
            ->setFilter($this)
            ->setLabel($label)
            ->setValue($value)
            ->setCount($count);
    }

    /**
     * Get all product ids from from collection with applied filters
     *
     * @return array
     */
    protected function _getFilterEntityIds()
    {
        return $this->getLayer()->getProductCollection()->getAllIdsCache();
    }

    /**
     * Get product collection select object with applied filters
     *
     * @return Magento_DB_Select
     */
    protected function _getBaseCollectionSql()
    {
        return $this->getLayer()->getProductCollection()->getSelect();
    }

    /**
     * Set attribute model to filter
     *
     * @param   Magento_Eav_Model_Entity_Attribute $attribute
     * @return  Magento_Catalog_Model_Layer_Filter_Abstract
     */
    public function setAttributeModel($attribute)
    {
        $this->setRequestVar($attribute->getAttributeCode());
        $this->setData('attribute_model', $attribute);
        return $this;
    }

    /**
     * Get attribute model associated with filter
     *
     * @return Magento_Catalog_Model_Resource_Eav_Attribute
     * @throws Magento_Core_Exception
     */
    public function getAttributeModel()
    {
        $attribute = $this->getData('attribute_model');
        if (is_null($attribute)) {
            throw new Magento_Core_Exception(__('The attribute model is not defined.'));
        }
        return $attribute;
    }

    /**
     * Get filter text label
     *
     * @return string
     */
    public function getName()
    {
        return $this->getAttributeModel()->getStoreLabel();
    }

    /**
     * Retrieve current store id scope
     *
     * @return int
     */
    public function getStoreId()
    {
        $storeId = $this->_getData('store_id');
        if (is_null($storeId)) {
            $storeId = $this->_storeManager->getStore()->getId();
        }
        return $storeId;
    }

    /**
     * Set store id scope
     *
     * @param int $storeId
     * @return Magento_Catalog_Model_Layer_Filter_Abstract
     */
    public function setStoreId($storeId)
    {
        return $this->setData('store_id', $storeId);
    }

    /**
     * Retrieve Website ID scope
     *
     * @return int
     */
    public function getWebsiteId()
    {
        $websiteId = $this->_getData('website_id');
        if (is_null($websiteId)) {
            $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        }
        return $websiteId;
    }

    /**
     * Set Website ID scope
     *
     * @param int $websiteId
     * @return Magento_Catalog_Model_Layer_Filter_Abstract
     */
    public function setWebsiteId($websiteId)
    {
        return $this->setData('website_id', $websiteId);
    }

    /**
     * Clear current element link text, for example 'Clear Price'
     *
     * @return false|string
     */
    public function getClearLinkText()
    {
        return false;
    }
}
