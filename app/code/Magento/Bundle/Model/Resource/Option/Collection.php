<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Bundle Options Resource Collection
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Bundle_Model_Resource_Option_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * All item ids cache
     *
     * @var array
     */
    protected $_itemIds;

    /**
     * True when selections a
     *
     * @var bool
     */
    protected $_selectionsAppended   = false;

    /**
     * Init model and resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Bundle_Model_Option', 'Magento_Bundle_Model_Resource_Option');
    }

    /**
     * Joins values to options
     *
     * @param int $storeId
     * @return Magento_Bundle_Model_Resource_Option_Collection
     */
    public function joinValues($storeId)
    {
        $this->getSelect()
            ->joinLeft(
                array('option_value_default' => $this->getTable('catalog_product_bundle_option_value')),
                'main_table.option_id = option_value_default.option_id and option_value_default.store_id = 0',
                array()
            )
            ->columns(array('default_title' => 'option_value_default.title'));

        $title = $this->getConnection()->getCheckSql(
            'option_value.title IS NOT NULL',
            'option_value.title',
            'option_value_default.title'
        );
        if ($storeId !== null) {
            $this->getSelect()
                ->columns(array('title' => $title))
                ->joinLeft(array('option_value' => $this->getTable('catalog_product_bundle_option_value')),
                    $this->getConnection()->quoteInto(
                        'main_table.option_id = option_value.option_id and option_value.store_id = ?',
                        $storeId
                    ),
                    array()
                );
        }
        return $this;
    }

    /**
     * Sets product id filter
     *
     * @param int $productId
     * @return Magento_Bundle_Model_Resource_Option_Collection
     */
    public function setProductIdFilter($productId)
    {
        $this->addFieldToFilter('main_table.parent_id', $productId);
        return $this;
    }

    /**
     * Sets order by position
     *
     * @return Magento_Bundle_Model_Resource_Option_Collection
     */
    public function setPositionOrder()
    {
        $this->getSelect()->order('main_table.position asc')
            ->order('main_table.option_id asc');
        return $this;
    }

    /**
     * Append selection to options
     * stripBefore - indicates to reload
     * appendAll - indicates do we need to filter by saleable and required custom options
     *
     * @param Magento_Bundle_Model_Resource_Selection_Collection $selectionsCollection
     * @param bool $stripBefore
     * @param bool $appendAll
     * @return array
     */
    public function appendSelections($selectionsCollection, $stripBefore = false, $appendAll = true)
    {
        if ($stripBefore) {
            $this->_stripSelections();
        }

        if (!$this->_selectionsAppended) {
            foreach ($selectionsCollection->getItems() as $key => $_selection) {
                if ($_option = $this->getItemById($_selection->getOptionId())) {
                    if ($appendAll || ($_selection->isSalable() && !$_selection->getRequiredOptions())) {
                        $_selection->setOption($_option);
                        $_option->addSelection($_selection);
                    } else {
                        $selectionsCollection->removeItemByKey($key);
                    }
                }
            }
            $this->_selectionsAppended = true;
        }

        return $this->getItems();
    }

    /**
     * Removes appended selections before
     *
     * @return Magento_Bundle_Model_Resource_Option_Collection
     */
    protected function _stripSelections()
    {
        foreach ($this->getItems() as $option) {
            $option->setSelections(array());
        }
        $this->_selectionsAppended = false;
        return $this;
    }

    /**
     * Sets filter by option id
     *
     * @param array|int $ids
     * @return Magento_Bundle_Model_Resource_Option_Collection
     */
    public function setIdFilter($ids)
    {
        if (is_array($ids)) {
            $this->addFieldToFilter('main_table.option_id', array('in' => $ids));
        } else if ($ids != '') {
            $this->addFieldToFilter('main_table.option_id', $ids);
        }
        return $this;
    }

    /**
     * Reset all item ids cache
     *
     * @return Magento_Bundle_Model_Resource_Option_Collection
     */
    public function resetAllIds()
    {
        $this->_itemIds = null;
        return $this;
    }

    /**
     * Retrive all ids for collection
     *
     * @return array
     */
    public function getAllIds()
    {
        if (is_null($this->_itemIds)) {
            $this->_itemIds = parent::getAllIds();
        }
        return $this->_itemIds;
    }
}

