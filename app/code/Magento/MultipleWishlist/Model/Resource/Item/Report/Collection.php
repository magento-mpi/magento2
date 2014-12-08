<?php
/**
 * Wishlist item report collection
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\MultipleWishlist\Model\Resource\Item\Report;

use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;

class Collection extends \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Catalog data
     *
     * @var \Magento\Catalog\Helper\Data
     */
    protected $_catalogData = null;

    /**
     * Wishlist data
     *
     * @var \Magento\Wishlist\Helper\Data
     */
    protected $_wishlistData = null;

    /**
     * @var \Magento\Framework\Object\Copy\Config
     */
    protected $_fieldsetConfig;

    /**
     * Customer resource model
     *
     * @var \Magento\Customer\Model\Resource\Customer
     */
    protected $_resourceCustomer;

    /**
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Framework\Logger $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\MultipleWishlist\Model\Resource\Item $itemResource
     * @param \Magento\Wishlist\Helper\Data $wishlistData
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Framework\Object\Copy\Config $fieldsetConfig
     * @param \Magento\Customer\Model\Resource\Customer $resourceCustomer
     * @param mixed $connection
     * @param \Magento\Framework\Model\Resource\Db\AbstractDb $resource
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Framework\Logger $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\MultipleWishlist\Model\Resource\Item $itemResource,
        \Magento\Wishlist\Helper\Data $wishlistData,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Framework\Object\Copy\Config $fieldsetConfig,
        \Magento\Customer\Model\Resource\Customer $resourceCustomer,
        $connection = null,
        \Magento\Framework\Model\Resource\Db\AbstractDb $resource = null
    ) {
        $this->_wishlistData = $wishlistData;
        $this->_catalogData = $catalogData;
        $this->_fieldsetConfig = $fieldsetConfig;
        $this->_resourceCustomer = $resourceCustomer;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Init model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\MultipleWishlist\Model\Item', 'Magento\MultipleWishlist\Model\Resource\Item');
    }

    /**
     * Add customer information to collection items
     *
     * @return $this
     */
    protected function _addCustomerInfo()
    {
        $customerAccount = $this->_fieldsetConfig->getFieldset('customer_account');

        foreach ($customerAccount as $code => $field) {
            if (isset($field['name'])) {
                $fields[$code] = $code;
            }
        }

        $adapter = $this->getConnection();
        $concatenate = [];
        if (isset($fields['prefix'])) {
            $this->_joinCustomerAttibute($this->_resourceCustomer->getAttribute('prefix'));
            $fields['prefix'] = 'at_prefix.value';
            $concatenate[] = $adapter->getCheckSql(
                '{{prefix}} IS NOT NULL AND {{prefix}} != \'\'',
                $adapter->getConcatSql(['LTRIM(RTRIM({{prefix}}))', '\' \'']),
                '\'\''
            );
        }
        $this->_joinCustomerAttibute($this->_resourceCustomer->getAttribute('firstname'));
        $fields['firstname'] = 'at_firstname.value';
        $concatenate[] = 'LTRIM(RTRIM({{firstname}}))';
        $concatenate[] = '\' \'';
        if (isset($fields['middlename'])) {
            $fields['middlename'] = 'at_middlename.value';
            $this->_joinCustomerAttibute($this->_resourceCustomer->getAttribute('middlename'));
            $concatenate[] = $adapter->getCheckSql(
                '{{middlename}} IS NOT NULL AND {{middlename}} != \'\'',
                $adapter->getConcatSql(['LTRIM(RTRIM({{middlename}}))', '\' \'']),
                '\'\''
            );
        }
        $this->_joinCustomerAttibute($this->_resourceCustomer->getAttribute('lastname'));
        $fields['lastname'] = 'at_lastname.value';
        $concatenate[] = 'LTRIM(RTRIM({{lastname}}))';
        if (isset($fields['suffix'])) {
            $this->_joinCustomerAttibute($this->_resourceCustomer->getAttribute('suffix'));
            $fields['suffix'] = 'at_suffix.value';
            $concatenate[] = $adapter->getCheckSql(
                '{{suffix}} IS NOT NULL AND {{suffix}} != \'\'',
                $adapter->getConcatSql(['\' \'', 'LTRIM(RTRIM({{suffix}}))']),
                '\'\''
            );
        }

        $nameExpr = $adapter->getConcatSql($concatenate);

        $this->addExpressionFieldToSelect('customer_name', $nameExpr, $fields);

        return $this;
    }

    /**
     * Join customer attribute
     *
     * @param AbstractAttribute $attribute
     * @return void
     */
    protected function _joinCustomerAttibute(AbstractAttribute $attribute)
    {
        $adapter = $this->getSelect()->getAdapter();
        $tableName = $adapter->getTableName('at_' . $attribute->getName());
        $joinExpr = [
            $tableName . '.entity_id = wishlist_table.customer_id',
            $adapter->quoteInto($tableName . '.attribute_id = ?', $attribute->getAttributeId()),
        ];
        $this->getSelect()->joinLeft(
            [$tableName => $attribute->getBackend()->getTable()],
            implode(' AND ', $joinExpr),
            []
        );
    }

    /**
     * Filter collection by store ids
     *
     * @param array $storeIds
     * @return $this
     */
    public function filterByStoreIds(array $storeIds)
    {
        $this->addFieldToFilter('main_table.store_id', ['in' => [$storeIds]]);
        return $this;
    }

    /**
     * Add product information to collection
     *
     * @return $this
     */
    protected function _addProductInfo()
    {
        if ($this->_catalogData->isModuleEnabled('Magento_CatalogInventory')) {
            $this->getSelect()->joinLeft(
                ['item_stock' => $this->getTable('cataloginventory_stock_item')],
                'main_table.product_id = item_stock.product_id',
                ['product_qty' => 'qty']
            );
            $this->getSelect()->columns(['qty_diff' => '(item_stock.qty - main_table.qty)']);
        }

        $this->addFilterToMap('product_qty', 'item_stock.qty');
        $this->addFilterToMap('qty_diff', '(item_stock.qty - main_table.qty)');
        return $this;
    }

    /**
     * Add selected data
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $select = $this->getSelect();
        $select->reset(
            \Zend_Db_Select::COLUMNS
        )->columns(
            ['item_qty' => 'qty', 'added_at', 'description', 'product_id']
        );

        $adapter = $this->getSelect()->getAdapter();
        $defaultWishlistName = $this->_wishlistData->getDefaultWishlistName();
        $this->getSelect()->join(
            ['wishlist_table' => $this->getTable('wishlist')],
            'main_table.wishlist_id = wishlist_table.wishlist_id',
            [
                'visibility' => 'visibility',
                'wishlist_name' => $adapter->getIfNullSql('name', $adapter->quote($defaultWishlistName))
            ]
        );

        $this->addFilterToMap(
            'wishlist_name',
            $adapter->getIfNullSql('name', $adapter->quote($defaultWishlistName))
        )->addFilterToMap(
            'item_qty',
            'main_table.qty'
        )->_addCustomerInfo()->_addProductInfo();

        return $this;
    }

    /**
     * Add product info to collection
     *
     * @return void
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        foreach ($this->_items as $item) {
            /* @var $item \Magento\MultipleWishlist\Model\Item $item*/
            $product = $item->getProduct();
            $item->setProductName($product->getName());
            $item->setProductPrice($product->getPrice());
            $item->setProductSku($product->getSku());
        }
    }
}
