<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Model\Resource\Option;

/**
 * Bundle Options Resource Collection
 */
class Collection extends \Magento\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * All item ids cache
     *
     * @var array
     */
    protected $_itemIds;

    /**
     * True when selections appended
     *
     * @var bool
     */
    protected $_selectionsAppended = false;

    /**
     * Init model and resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Bundle\Model\Option', 'Magento\Bundle\Model\Resource\Option');
    }

    /**
     * Joins values to options
     *
     * @param int $storeId
     * @return $this
     */
    public function joinValues($storeId)
    {
        $this->getSelect()->joinLeft(
            array('option_value_default' => $this->getTable('catalog_product_bundle_option_value')),
            'main_table.option_id = option_value_default.option_id and option_value_default.store_id = 0',
            array()
        )->columns(
            array('default_title' => 'option_value_default.title')
        );

        $title = $this->getConnection()->getCheckSql(
            'option_value.title IS NOT NULL',
            'option_value.title',
            'option_value_default.title'
        );
        if ($storeId !== null) {
            $this->getSelect()->columns(
                array('title' => $title)
            )->joinLeft(
                array('option_value' => $this->getTable('catalog_product_bundle_option_value')),
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
     * @return $this
     */
    public function setProductIdFilter($productId)
    {
        $this->addFieldToFilter('main_table.parent_id', $productId);
        return $this;
    }

    /**
     * Sets order by position
     *
     * @return $this
     */
    public function setPositionOrder()
    {
        $this->getSelect()->order('main_table.position asc')->order('main_table.option_id asc');
        return $this;
    }

    /**
     * Append selection to options
     * stripBefore - indicates to reload
     * appendAll - indicates do we need to filter by saleable and required custom options
     *
     * @param \Magento\Bundle\Model\Resource\Selection\Collection $selectionsCollection
     * @param bool $stripBefore
     * @param bool $appendAll
     * @return \Magento\Object[]
     */
    public function appendSelections($selectionsCollection, $stripBefore = false, $appendAll = true)
    {
        if ($stripBefore) {
            $this->_stripSelections();
        }

        if (!$this->_selectionsAppended) {
            foreach ($selectionsCollection->getItems() as $key => $selection) {
                $option = $this->getItemById($selection->getOptionId());
                if ($option) {
                    if ($appendAll || $selection->isSalable() && !$selection->getRequiredOptions()) {
                        $selection->setOption($option);
                        $option->addSelection($selection);
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
     * @return $this
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
     * @return $this
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
     * @return $this
     */
    public function resetAllIds()
    {
        $this->_itemIds = null;
        return $this;
    }

    /**
     * Retrieve all ids for collection
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
