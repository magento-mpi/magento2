<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Model\Grouped;

class Collection
    extends \Magento\Data\Collection //\Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Column name for group by clause 
     *
     * @var string
     */
    protected $_columnGroupBy       = null;

    /**
     * Collection resource
     *
     * @var \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
     */
    protected $_resourceCollection  = null;

    /**
     * Set column to group by
     *
     * @param string $column
     * @return \Magento\Reports\Model\Grouped\Collection
     */
    public function setColumnGroupBy($column)
    {
        $this->_columnGroupBy = (string)$column;
        return $this;
    }

    /**
     * Load collection
     *
     * @param boolean $printQuery
     * @param boolean $logQuery
     * @return \Magento\Reports\Model\Grouped\Collection
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }

        parent::load($printQuery, $logQuery);
        $this->_setIsLoaded();

        if ($this->_columnGroupBy !== null) {
            $this->_mergeWithEmptyData();
            $this->_groupResourceData();
        }

        return $this;
    }

    /**
     * Setter for resource collection
     *
     * @param \Magento\Data\Collection\Db $collection
     * @return \Magento\Reports\Model\Grouped\Collection
     */
    public function setResourceCollection($collection)
    {
        $this->_resourceCollection = $collection;
        return $this;
    }

    /**
     * Merge empty data collection with resource collection
     *
     * @return \Magento\Reports\Model\Grouped\Collection
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
     * @return \Magento\Reports\Model\Grouped\Collection
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
