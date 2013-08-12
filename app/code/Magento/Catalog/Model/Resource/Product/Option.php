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
 * Catalog product custom option resource model
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Resource_Product_Option extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Define main table and initialize connection
     *
     */
    protected function _construct()
    {
        $this->_init('catalog_product_option', 'option_id');
    }

    /**
     * Save options store data
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_Core_Model_Resource_Db_Abstract
     */
    protected function _afterSave(Magento_Core_Model_Abstract $object)
    {
        $this->_saveValuePrices($object);
        $this->_saveValueTitles($object);

        return parent::_afterSave($object);
    }

    /**
     * Save value prices
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_Catalog_Model_Resource_Product_Option
     */
    protected function _saveValuePrices(Magento_Core_Model_Abstract $object)
    {
        $priceTable   = $this->getTable('catalog_product_option_price');
        $readAdapter  = $this->_getReadAdapter();
        $writeAdapter = $this->_getWriteAdapter();

        /*
         * Better to check param 'price' and 'price_type' for saving.
         * If there is not price skip saving price
         */

        if ($object->getType() == Magento_Catalog_Model_Product_Option::OPTION_TYPE_FIELD
            || $object->getType() == Magento_Catalog_Model_Product_Option::OPTION_TYPE_AREA
            || $object->getType() == Magento_Catalog_Model_Product_Option::OPTION_TYPE_FILE
            || $object->getType() == Magento_Catalog_Model_Product_Option::OPTION_TYPE_DATE
            || $object->getType() == Magento_Catalog_Model_Product_Option::OPTION_TYPE_DATE_TIME
            || $object->getType() == Magento_Catalog_Model_Product_Option::OPTION_TYPE_TIME
        ) {
            //save for store_id = 0
            if (!$object->getData('scope', 'price')) {
                $statement = $readAdapter->select()
                    ->from($priceTable, 'option_id')
                    ->where('option_id = ?', $object->getId())
                    ->where('store_id = ?', Magento_Catalog_Model_Abstract::DEFAULT_STORE_ID);
                $optionId = $readAdapter->fetchOne($statement);

                if ($optionId) {
                    if ($object->getStoreId() == '0') {
                        $data = $this->_prepareDataForTable(
                            new Magento_Object(
                                array(
                                    'price'      => $object->getPrice(),
                                    'price_type' => $object->getPriceType())
                            ),
                            $priceTable
                        );

                        $writeAdapter->update(
                            $priceTable,
                            $data,
                            array(
                                'option_id = ?' => $object->getId(),
                                'store_id  = ?' => Magento_Catalog_Model_Abstract::DEFAULT_STORE_ID,
                            )
                        );
                    }
                } else {
                    $data = $this->_prepareDataForTable(
                         new Magento_Object(
                            array(
                                'option_id'  => $object->getId(),
                                'store_id'   => Magento_Catalog_Model_Abstract::DEFAULT_STORE_ID,
                                'price'      => $object->getPrice(),
                                'price_type' => $object->getPriceType()
                            )
                        ),
                        $priceTable
                    );
                    $writeAdapter->insert($priceTable, $data);
                }
            }

            $scope = (int) Mage::app()->getStore()->getConfig(Magento_Core_Model_Store::XML_PATH_PRICE_SCOPE);

            if ($object->getStoreId() != '0' && $scope == Magento_Core_Model_Store::PRICE_SCOPE_WEBSITE
                && !$object->getData('scope', 'price')) {

                $baseCurrency = Mage::app()->getBaseCurrencyCode();

                $storeIds = Mage::app()->getStore($object->getStoreId())->getWebsite()->getStoreIds();
                if (is_array($storeIds)) {
                    foreach ($storeIds as $storeId) {
                        if ($object->getPriceType() == 'fixed') {
                            $storeCurrency = Mage::app()->getStore($storeId)->getBaseCurrencyCode();
                            $rate = Mage::getModel('Magento_Directory_Model_Currency')->load($baseCurrency)->getRate($storeCurrency);
                            if (!$rate) {
                                $rate=1;
                            }
                            $newPrice = $object->getPrice() * $rate;
                        } else {
                            $newPrice = $object->getPrice();
                        }

                        $statement = $readAdapter->select()
                            ->from($priceTable)
                            ->where('option_id = ?', $object->getId())
                            ->where('store_id  = ?', $storeId);

                        if ($readAdapter->fetchOne($statement)) {
                            $data = $this->_prepareDataForTable(
                                new Magento_Object(
                                    array(
                                        'price'      => $newPrice,
                                        'price_type' => $object->getPriceType()
                                    )
                                ),
                                $priceTable
                            );

                            $writeAdapter->update(
                                $priceTable,
                                $data,
                                array(
                                    'option_id = ?' => $object->getId(),
                                    'store_id  = ?' => $storeId
                                )
                            );
                        } else {
                            $data = $this->_prepareDataForTable(
                                new Magento_Object(
                                    array(
                                        'option_id'  => $object->getId(),
                                        'store_id'   => $storeId,
                                        'price'      => $newPrice,
                                        'price_type' => $object->getPriceType()
                                    )
                                ),
                                $priceTable
                            );
                            $writeAdapter->insert($priceTable, $data);
                        }
                    }// end foreach()
                }
            } elseif ($scope == Magento_Core_Model_Store::PRICE_SCOPE_WEBSITE && $object->getData('scope', 'price')) {
                $writeAdapter->delete(
                    $priceTable,
                    array(
                        'option_id = ?' => $object->getId(),
                        'store_id  = ?' => $object->getStoreId()
                    )
                );
            }
        }

        return $this;
    }

    /**
     * Save titles
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_Catalog_Model_Resource_Product_Option
     */
    protected function _saveValueTitles(Magento_Core_Model_Abstract $object)
    {
        $readAdapter  = $this->_getReadAdapter();
        $writeAdapter = $this->_getWriteAdapter();
        $titleTable = $this->getTable('catalog_product_option_title');

        //title
        if (!$object->getData('scope', 'title')) {
            $statement = $readAdapter->select()
                ->from($titleTable)
                ->where('option_id = ?', $object->getId())
                ->where('store_id  = ?', Magento_Catalog_Model_Abstract::DEFAULT_STORE_ID);

            if ($readAdapter->fetchOne($statement)) {
                if ($object->getStoreId() == '0') {
                    $data = $this->_prepareDataForTable(
                        new Magento_Object(
                            array(
                                'title' => $object->getTitle()
                            )
                        ),
                        $titleTable
                    );

                    $writeAdapter->update(
                        $titleTable,
                        $data,
                        array(
                            'option_id = ?' => $object->getId(),
                            'store_id  = ?' => Magento_Catalog_Model_Abstract::DEFAULT_STORE_ID
                        )
                    );
                }
            } else {
                $data = $this->_prepareDataForTable(
                    new Magento_Object(
                        array(
                            'option_id' => $object->getId(),
                            'store_id'  => Magento_Catalog_Model_Abstract::DEFAULT_STORE_ID,
                            'title'     => $object->getTitle()
                        )
                    ),
                    $titleTable
                );

                $writeAdapter->insert($titleTable, $data);
            }
        }

        if ($object->getStoreId() != '0' && !$object->getData('scope', 'title')) {
            $statement = $readAdapter->select()
                ->from($titleTable)
                ->where('option_id = ?', $object->getId())
                ->where('store_id  = ?', $object->getStoreId());

            if ($readAdapter->fetchOne($statement)) {
                $data = $this->_prepareDataForTable(
                    new Magento_Object(
                        array(
                            'title' => $object->getTitle()
                        )
                    ),
                    $titleTable
                );

                $writeAdapter->update(
                    $titleTable,
                    $data,
                    array(
                        'option_id = ?' => $object->getId(),
                        'store_id  = ?' => $object->getStoreId()
                    )
                );
            } else {
                $data = $this->_prepareDataForTable(
                    new Magento_Object(
                        array(
                            'option_id' => $object->getId(),
                            'store_id'  => $object->getStoreId(),
                            'title'     => $object->getTitle()
                        )
                    ),
                    $titleTable
                );
                $writeAdapter->insert($titleTable, $data);
            }
        } elseif ($object->getData('scope', 'title')) {
            $writeAdapter->delete(
                $titleTable,
                array(
                    'option_id = ?' => $object->getId(),
                    'store_id  = ?' => $object->getStoreId()
                )
            );
        }
    }

    /**
     * Delete prices
     *
     * @param int $optionId
     * @return Magento_Catalog_Model_Resource_Product_Option
     */
    public function deletePrices($optionId)
    {
        $this->_getWriteAdapter()->delete(
            $this->getTable('catalog_product_option_price'),
            array(
                'option_id = ?' => $optionId
            )
        );

        return $this;
    }

    /**
     * Delete titles
     *
     * @param int $optionId
     * @return Magento_Catalog_Model_Resource_Product_Option
     */
    public function deleteTitles($optionId)
    {
        $this->_getWriteAdapter()->delete(
            $this->getTable('catalog_product_option_title'),
            array(
                'option_id = ?' => $optionId
            )
        );

        return $this;
    }

    /**
     * Duplicate custom options for product
     *
     * @param Magento_Catalog_Model_Product_Option $object
     * @param int $oldProductId
     * @param int $newProductId
     * @return Magento_Catalog_Model_Product_Option
     */
    public function duplicate(Magento_Catalog_Model_Product_Option $object, $oldProductId, $newProductId)
    {
        $write  = $this->_getWriteAdapter();
        $read   = $this->_getReadAdapter();

        $optionsCond = array();
        $optionsData = array();

        // read and prepare original product options
        $select = $read->select()
            ->from($this->getTable('catalog_product_option'))
            ->where('product_id = ?', $oldProductId);

        $query = $read->query($select);

        while ($row = $query->fetch()) {
            $optionsData[$row['option_id']] = $row;
            $optionsData[$row['option_id']]['product_id'] = $newProductId;
            unset($optionsData[$row['option_id']]['option_id']);
        }

        // insert options to duplicated product
        foreach ($optionsData as $oId => $data) {
            $write->insert($this->getMainTable(), $data);
            $optionsCond[$oId] = $write->lastInsertId($this->getMainTable());
        }

        // copy options prefs
        foreach ($optionsCond as $oldOptionId => $newOptionId) {
            // title
            $table = $this->getTable('catalog_product_option_title');

            $select = $this->_getReadAdapter()->select()
                ->from($table, array(new Zend_Db_Expr($newOptionId), 'store_id', 'title'))
                ->where('option_id = ?', $oldOptionId);

            $insertSelect = $write->insertFromSelect(
                $select,
                $table,
                array('option_id', 'store_id', 'title'),
                Magento_DB_Adapter_Interface::INSERT_ON_DUPLICATE
            );
            $write->query($insertSelect);

            // price
            $table = $this->getTable('catalog_product_option_price');

            $select = $read->select()
                ->from($table, array(new Zend_Db_Expr($newOptionId), 'store_id', 'price', 'price_type'))
                ->where('option_id = ?', $oldOptionId);

            $insertSelect = $write->insertFromSelect(
                $select, $table,
                array(
                    'option_id',
                    'store_id',
                    'price',
                    'price_type'
                ),
                Magento_DB_Adapter_Interface::INSERT_ON_DUPLICATE
            );
            $write->query($insertSelect);

            $object->getValueInstance()->duplicate($oldOptionId, $newOptionId);
        }

        return $object;
    }

    /**
     * Retrieve option searchable data
     *
     * @param int $productId
     * @param int $storeId
     * @return array
     */
    public function getSearchableData($productId, $storeId)
    {
        $searchData = array();

        $adapter = $this->_getReadAdapter();

        $titleCheckSql = $adapter->getCheckSql(
            'option_title_store.title IS NULL',
            'option_title_default.title',
            'option_title_store.title'
        );


        // retrieve options title

        $defaultOptionJoin = implode(
            ' AND ',
            array('option_title_default.option_id=product_option.option_id',
            $adapter->quoteInto('option_title_default.store_id = ?', Magento_Catalog_Model_Abstract::DEFAULT_STORE_ID))
        );

        $storeOptionJoin = implode(
            ' AND ',
            array(
                'option_title_store.option_id=product_option.option_id',
                $adapter->quoteInto('option_title_store.store_id = ?', (int) $storeId))
        );

        $select = $adapter->select()
            ->from(array('product_option' => $this->getMainTable()), null)
            ->join(
                array('option_title_default' => $this->getTable('catalog_product_option_title')),
                $defaultOptionJoin,
                array()
            )
            ->joinLeft(
                array('option_title_store' => $this->getTable('catalog_product_option_title')),
                $storeOptionJoin,
                array('title' => $titleCheckSql)
            )
            ->where('product_option.product_id = ?', $productId);

        if ($titles = $adapter->fetchCol($select)) {
            $searchData = array_merge($searchData, $titles);
        }

        //select option type titles

        $defaultOptionJoin = implode(
            ' AND ', array(
                'option_title_default.option_type_id=option_type.option_type_id',
                $adapter->quoteInto('option_title_default.store_id = ?', Magento_Catalog_Model_Abstract::DEFAULT_STORE_ID))
        );

        $storeOptionJoin = implode(
            ' AND ', array(
                'option_title_store.option_type_id = option_type.option_type_id',
                 $adapter->quoteInto('option_title_store.store_id = ?', (int) $storeId))
        );

        $select = $adapter->select()
            ->from(array('product_option' => $this->getMainTable()), null)
            ->join(
                array('option_type' => $this->getTable('catalog_product_option_type_value')),
                'option_type.option_id=product_option.option_id',
                array()
            )
            ->join(
                array('option_title_default' => $this->getTable('catalog_product_option_type_title')),
                $defaultOptionJoin,
                array()
            )
            ->joinLeft(
                array('option_title_store' => $this->getTable('catalog_product_option_type_title')),
                $storeOptionJoin,
                array('title' => $titleCheckSql)
            )
            ->where('product_option.product_id = ?', $productId);

        if ($titles = $adapter->fetchCol($select)) {
            $searchData = array_merge($searchData, $titles);
        }

        return $searchData;
    }
}
