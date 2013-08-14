<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customers collection
 *
 * @category   Magento
 * @package    Magento_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Tag_Model_Entity_Customer_Collection extends Magento_Customer_Model_Resource_Customer_Collection
{
    protected $_tagTable;
    protected $_tagRelTable;

    public function __construct(
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_Resource $resource
    ) {
        parent::__construct($fetchStrategy);
        $this->_tagTable = $resource->getTableName('tag');
        $this->_tagRelTable = $resource->getTableName('tag_relation');
    }

    public function addTagFilter($tagId)
    {
        $this->joinField('tag_tag_id', $this->_tagRelTable, 'tag_id', 'customer_id=entity_id');
        $this->getSelect()->where($this->_getAttributeTableAlias('tag_tag_id') . '.tag_id=?', $tagId);
        return $this;
    }

    public function addProductFilter($productId)
    {
        $this->joinField('tag_product_id', $this->_tagRelTable, 'product_id', 'customer_id=entity_id');
        $this->getSelect()->where($this->_getAttributeTableAlias('tag_product_id') . '.product_id=?', $productId);
        return $this;
    }

    public function load($printQuery = false, $logQuery = false)
    {
        parent::load($printQuery, $logQuery);
        $this->_loadTags($printQuery, $logQuery);
        return $this;
    }

    protected function _loadTags($printQuery = false, $logQuery = false)
    {
        if (empty($this->_items)) {
            return $this;
        }
        $customerIds = array();
        foreach ($this->getItems() as $item) {
            $customerIds[] = $item->getId();
        }
        $this->getSelect()->reset()
            ->from(array('tr' => $this->_tagRelTable), array('*','total_used' => 'count(tr.tag_relation_id)'))
            ->joinLeft(array('t' => $this->_tagTable),'t.tag_id=tr.tag_id')
            ->group(array('tr.customer_id', 't.tag_id'))
            ->where('tr.customer_id in (?)',$customerIds)
        ;
        $this->printLogQuery($printQuery, $logQuery);

        $tags = array();
        $data = $this->_read->fetchAll($this->getSelect());
        foreach ($data as $row) {
            if (!isset($tags[ $row['customer_id'] ])) {
                $tags[ $row['customer_id'] ] = array();
            }
            $tags[ $row['customer_id'] ][] = $row;
        }
        foreach ($this->getItems() as $item) {
            if (isset($tags[$item->getId()])) {
                $item->setData('tags', $tags[$item->getId()]);
            }
        }
        return $this;
    }

}
