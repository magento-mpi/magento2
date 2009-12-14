<?php
class Mage_Reports_Model_Grouped_Collection extends Varien_Data_Collection
{
    /**
     * Enter description here...
     *
     * @var unknown_type
     */
    protected $_columnGroupBy       = null;

    /**
     * Enter description here...
     *
     * @var unknown_type
     */
    protected $_resourceCollection  = array();

    /**
     * Set column to group by
     *
     * @param string $column
     * @return Mage_Reports_Model_Grouped_Collection
     */
    public function setColumnGroupBy($column)
    {
        $this->_columnGroupBy = $column;
        return $this;
    }

    /**
     * Load collection
     *
     * @param boolean $printQuery
     * @param boolean$logQuery
     * @return Mage_Reports_Model_Grouped_Collection
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }

        parent::load($printQuery, $logQuery);
        $this->_setIsLoaded();

        if (!is_null($this->_columnGroupBy)) {
            $this->_mergeWithEmptyData();
            $this->_groupResourceData();
        }

        return $this;
    }

    /**
     * Setter for resource collection
     *
     * @param unknown_type $collection
     * @return Mage_Reports_Model_Grouped_Collection
     */
    public function setResourceCollection($collection)
    {
        $this->_resourceCollection = $collection;
        return $this;
    }

    /**
     * Merge empty data collection with resource collection
     *
     * @return Mage_Reports_Model_Grouped_Collection
     */
    protected function _mergeWithEmptyData()
    {
        if (count($this->_items) == 0) {
            return $this;
        }

        foreach ($this->_items as $key => $item) {
            foreach ($this->_resourceCollection as $dataItem) {
                if ($item->getData($this->_columnGroupBy) == $dataItem->getData($this->_columnGroupBy)) {
                    if ($this->_items[$key]->getIsEmpty()) {
                        $this->_items[$key] = $dataItem;
                    } else {
                        $this->_items[$key]->addChild($dataItem);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Group data in resource collection
     *
     * @return Mage_Reports_Model_Grouped_Collection
     */
    protected function _groupResourceData()
    {
        if (count($this->_items) == 0) {
            foreach ($this->_resourceCollection as $item) {
                if (isset($this->_items[$item->getData($this->_columnGroupBy)])) {
                    $this->_items[$item->getData($this->_columnGroupBy)]->addChild($item->setIsEmpty(false));
                } else {
                    $this->_items[$item->getData($this->_columnGroupBy)] = $item->setIsEmpty(false);
                }
            }
        }

        return $this;
    }
}
