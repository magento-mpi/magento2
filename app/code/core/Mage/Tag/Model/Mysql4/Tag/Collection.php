<?php
class Mage_Tag_Model_Mysql4_Tag_Collection extends Varien_Data_Collection_Db
{
	protected $_tagTable;
    protected $_tagRelTable;

    public function __construct()
    {
        $resources = Mage::getSingleton('core/resource');

        parent::__construct($resources->getConnection('tag_read'));

        $this->_tagTable = $resources->getTableName('tag/tag');
        $this->_tagRelTable = $resources->getTableName('tag/tag_relation');

        $this->_sqlSelect->from($this->_tagTable, array('*', 'total_used' => 'COUNT(tag_relation_id)'))
            ->joinLeft($this->_tagRelTable, $this->_tagTable.'.tag_id='.$this->_tagRelTable.'.tag_id', 'tag_relation_id')
            ->group($this->_tagTable.'.tag_id');

        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('tag/tag'));
    }

    public function addAttributeToFilter()
    {
        // TODO
    }
}
