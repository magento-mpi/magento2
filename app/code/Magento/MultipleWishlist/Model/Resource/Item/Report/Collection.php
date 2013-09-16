<?php
/**
 * Wishlist item report collection
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_MultipleWishlist_Model_Resource_Item_Report_Collection
    extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Catalog data
     *
     * @var Magento_Catalog_Helper_Data
     */
    protected $_catalogData = null;

    /**
     * Wishlist data
     *
     * @var Magento_Wishlist_Helper_Data
     */
    protected $_wishlistData = null;

    /**
     * @var Magento_Core_Model_Fieldset_Config
     */
    protected $_fieldsetConfig;

    /**
     * Collection constructor
     *
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Wishlist_Helper_Data $wishlistData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Model_Fieldset_Config $fieldsetConfig
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_EntityFactory $entityFactory
     * @param Magento_MultipleWishlist_Model_Resource_Item $resource
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Wishlist_Helper_Data $wishlistData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Model_Fieldset_Config $fieldsetConfig,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_EntityFactory $entityFactory,
        Magento_MultipleWishlist_Model_Resource_Item $resource
    ) {
        $this->_wishlistData = $wishlistData;
        $this->_catalogData = $catalogData;
        $this->_fieldsetConfig = $fieldsetConfig;
        parent::__construct($eventManager, $fetchStrategy, $entityFactory, $resource);
    }

    /**
     * Init model
     */
    protected function _construct()
    {
        $this->_init('Magento_MultipleWishlist_Model_Item', 'Magento_MultipleWishlist_Model_Resource_Item');
    }

    /**
     * Add customer information to collection items
     *
     * @return Magento_MultipleWishlist_Model_Resource_Item_Report_Collection
     */
    protected function _addCustomerInfo()
    {
        /* @var Magento_Customer_Model_Resource_Customer $customer */
        $customer  = Mage::getResourceSingleton('Magento_Customer_Model_Resource_Customer');

        $customerAccount = $this->_fieldsetConfig->getFieldset('customer_account');

        foreach ($customerAccount as $code => $field) {
            if (isset($field['name'])) {
                $fields[$code] = $code;
            }
        }

        $adapter = $this->getConnection();
        $concatenate = array();
        if (isset($fields['prefix'])) {
            $this->_joinCustomerAttibute($customer->getAttribute('prefix'));
            $fields['prefix'] = 'at_prefix.value';
            $concatenate[] = $adapter->getCheckSql(
                '{{prefix}} IS NOT NULL AND {{prefix}} != \'\'',
                $adapter->getConcatSql(array('LTRIM(RTRIM({{prefix}}))', '\' \'')),
                '\'\''
            );
        }
        $this->_joinCustomerAttibute($customer->getAttribute('firstname'));
        $fields['firstname'] = 'at_firstname.value';
        $concatenate[] = 'LTRIM(RTRIM({{firstname}}))';
        $concatenate[] = '\' \'';
        if (isset($fields['middlename'])) {
            $fields['middlename'] = 'at_middlename.value';
            $this->_joinCustomerAttibute($customer->getAttribute('middlename'));
            $concatenate[] = $adapter->getCheckSql(
                '{{middlename}} IS NOT NULL AND {{middlename}} != \'\'',
                $adapter->getConcatSql(array('LTRIM(RTRIM({{middlename}}))', '\' \'')),
                '\'\'');
        }
        $this->_joinCustomerAttibute($customer->getAttribute('lastname'));
        $fields['lastname'] = 'at_lastname.value';
        $concatenate[] = 'LTRIM(RTRIM({{lastname}}))';
        if (isset($fields['suffix'])) {
            $this->_joinCustomerAttibute($customer->getAttribute('suffix'));
            $fields['suffix'] = 'at_suffix.value';
            $concatenate[] = $adapter
                    ->getCheckSql('{{suffix}} IS NOT NULL AND {{suffix}} != \'\'',
                $adapter->getConcatSql(array('\' \'', 'LTRIM(RTRIM({{suffix}}))')),
                '\'\'');
        }

        $nameExpr = $adapter->getConcatSql($concatenate);

        $this->addExpressionFieldToSelect('customer_name', $nameExpr, $fields);

        return $this;
    }

    /**
     * Join customer attribute
     *
     * @param Magento_Eav_Model_Entity_Attribute_Abstract $attribute
     */
    protected function _joinCustomerAttibute(Magento_Eav_Model_Entity_Attribute_Abstract $attribute)
    {
        $adapter = $this->getSelect()->getAdapter();
        $tableName = $adapter->getTableName('at_' . $attribute->getName());
        $joinExpr = array(
            $tableName . '.entity_id = wishlist_table.customer_id',
            $adapter->quoteInto(
                $tableName . '.attribute_id = ?', $attribute->getAttributeId()
            )
        );
        $this->getSelect()->joinLeft(
            array(
                $tableName => $attribute->getBackend()->getTable()
            ),
            implode(' AND ', $joinExpr),
            array()
        );
    }

    /**
     * Filter collection by store ids
     *
     * @param array $storeIds
     * @return Magento_MultipleWishlist_Model_Resource_Item_Report_Collection
     */
    public function filterByStoreIds(array $storeIds)
    {
        $this->addFieldToFilter('main_table.store_id', array('in' => array($storeIds)));
        return $this;
    }

    /**
     * Add product information to collection
     *
     * @return Magento_MultipleWishlist_Model_Resource_Item_Report_Collection
     */
    protected function _addProductInfo()
    {
        if ($this->_catalogData->isModuleEnabled('Magento_CatalogInventory')) {
            $this->getSelect()->joinLeft(
                array('item_stock' => $this->getTable('cataloginventory_stock_item')),
                'main_table.product_id = item_stock.product_id',
                array('product_qty' => 'qty')
            );
            $this->getSelect()->columns(array('qty_diff' => '(item_stock.qty - main_table.qty)'));
        }

        $this->addFilterToMap('product_qty', 'item_stock.qty');
        $this->addFilterToMap('qty_diff', '(item_stock.qty - main_table.qty)');
        return $this;
    }

    /**
     * Add selected data
     *
     * @return Magento_MultipleWishlist_Model_Resource_Item_Report_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $select = $this->getSelect();
        $select->reset(Zend_Db_Select::COLUMNS)
            ->columns(array('item_qty' => 'qty', 'added_at', 'description', 'product_id'));

        $adapter = $this->getSelect()->getAdapter();
        $defaultWishlistName = $this->_wishlistData->getDefaultWishlistName();
        $this->getSelect()->join(
            array('wishlist_table' => $this->getTable('wishlist')),
            'main_table.wishlist_id = wishlist_table.wishlist_id',
            array(
                'visibility' => 'visibility',
                'wishlist_name' => $adapter->getIfNullSql('name', $adapter->quote($defaultWishlistName))
            )
        );

        $this->addFilterToMap('wishlist_name', $adapter->getIfNullSql('name', $adapter->quote($defaultWishlistName)))
            ->addFilterToMap('item_qty', 'main_table.qty')
            ->_addCustomerInfo()
            ->_addProductInfo();

        return $this;
    }

    /**
     * Add product info to collection
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        foreach($this->_items as $item) {
            /* @var $item Magento_MultipleWishlist_Model_Item $item*/
            $product = $item->getProduct();
            $item->setProductName($product->getName());
            $item->setProductPrice($product->getPrice());
            $item->setProductSku($product->getSku());
        }
    }
}
