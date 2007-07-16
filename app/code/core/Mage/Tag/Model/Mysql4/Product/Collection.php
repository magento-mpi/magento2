<?php
/**
 * Tagged products collection.
 *
 * @package     Mage
 * @subpackage  Tag
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Tag_Model_Mysql4_Product_Collection extends Mage_Catalog_Model_Mysql4_Product_Collection
{
	protected $_tagTable;
    protected $_tagRelTable;

    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        parent::__construct($resource->getConnection('tag_read'));
        $this->_tagTable = $resource->getTableName('tag/tag');
        $this->_tagRelTable = $resource->getTableName('tag/tag_relation');
        // $this->_productTable = $resource->getTableName('catalog/product');
        // $this->_sqlSelect->from(array('p' => $this->_productTable))
        // ->join(array('tr' => $this->_tagRelTable), 'tr.entity_val_id=p.product_id and tr.entity_id=1', array('total_used' => 'count(tr.tag_relations_id)'))
        // ->group('p.product_id', 'tr.tag_id')
        // ;
        $this->_sqlSelect->join(array('tr' => $this->_tagRelTable), "{$this->_productTable}.product_id=tr.product_id");
    	$this->_sqlSelect->group("{$this->_productTable}.product_id");
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
        $productIds = array();
        foreach ($this->getItems() as $item) {
            $productIds[] = $item->getId();
        }
        $this->_sqlSelect->reset()
            ->from(array('tr' => $this->_tagRelTable), array('*','total_used' => 'count(tr.tag_relation_id)'))
            ->joinLeft(array('t' => $this->_tagTable),'t.tag_id=tr.tag_id')
            ->group(array('tr.product_id', 'tr.tag_id'))
            ->where('tr.product_id in (?)',$productIds)
        ;
        $this->printLogQuery($printQuery, $logQuery);

        $tags = array();
        $data = $this->getConnection()->fetchAll($this->_sqlSelect);
        foreach ($data as $row) {
            if (!isset($tags[ $row['product_id'] ])) {
                $tags[ $row['product_id'] ] = array();
            }
            $tags[ $row['product_id'] ][] = $row;
        }
        foreach ($this->getItems() as $item) {
            if (isset($tags[$item->getId()])) {
                $item->setData('tags', $tags[$item->getId()]);
            }
        }
        return $this;
    }

    public function addTagFilter($tagId)
    {
        if ($tagId) $this->_sqlSelect->where('tr.tag_id=?', $tagId);
        return $this;
    }

    public function addCustomerFilter($customerId)
    {
        if ($customerId) $this->_sqlSelect->where('tr.customer_id=?', $customerId);
        return $this;
    }

}