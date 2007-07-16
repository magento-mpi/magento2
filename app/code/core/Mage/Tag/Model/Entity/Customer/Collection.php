<?php
/**
 * Customers collection
 *
 * @package    Mage
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Tag_Model_Entity_Customer_Collection extends Mage_Customer_Model_Entity_Customer_Collection
{
	protected $_tagTable;
    protected $_tagRelTable;
    protected $_tagEntityTable;

    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        parent::__construct();
        $this->_tagTable = $resource->getTableName('tag/tag');
        $this->_tagRelTable = $resource->getTableName('tag/tag_relations');
        $this->_tagEntityTable = $resource->getTableName('tag/tag_entity');

        $this->joinField('tag_id', $this->_tagRelTable, 'tag_id', 'entity_val_id=entity_id', array('entity_id' => '2'));
        $this->joinField('tag_total_used', $this->_tagRelTable, 'count(_table_tag_total_used.tag_relations_id)', 'entity_val_id=entity_id', array('entity_id' => '2'));
        $this->getSelect()->group('tag_id');
//        $this->_productTable = $resource->getTableName('catalog/product');
//        $this->_sqlSelect->from(array('p' => $this->_productTable))
//            ->join(array('tr' => $this->_tagRelTable), 'tr.entity_val_id=p.product_id and tr.entity_id=1', array('total_used' => 'count(tr.tag_relations_id)'))
//            ->group('p.product_id', 'tr.tag_id')
//        ;
    }

    public function load($printQuery = false, $logQuery = false)
    {
        parent::load($printQuery, $logQuery);
        $this->_loadTags($printQuery, $logQuery);
        return $this;
    }

    protected function _loadTags($printQuery = false, $logQuery = false)
    {
        $customerIds = array();
        foreach ($this->getItems() as $item) {
            $customerIds[] = $item->getId();
        }
        $this->getSelect()->reset()
            ->from(array('tr' => $this->_tagRelTable), array('*','total_used' => 'count(tr.tag_relations_id)'))
            ->joinLeft(array('t' => $this->_tagTable),'t.tag_id=tr.tag_id')
            ->group(array('tr.entity_val_id', 't.tag_id'))
            ->where('tr.entity_id=2')
            ->where('tr.entity_val_id in (?)',$customerIds)
        ;
        $this->printLogQuery($printQuery, $logQuery);

        $tags = array();
        $data = $this->_read->fetchAll($this->getSelect());
        foreach ($data as $row) {
            if (!isset($tags[ $row['entity_val_id'] ])) {
                $tags[ $row['entity_val_id'] ] = array();
            }
            $tags[ $row['entity_val_id'] ][] = $row;
        }
        foreach ($this->getItems() as $item) {
            if (isset($tags[$item->getId()])) {
                $item->setData('tags', $tags[$item->getId()]);
            }
        }
    }

}