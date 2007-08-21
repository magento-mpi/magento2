<?php
/**
 * Tags customer collection
 *
 * @package     Mage
 * @subpackage  Tag
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Tag_Model_Mysql4_Customer_Collection extends Mage_Customer_Model_Entity_Customer_Collection
{
    protected $_allowDisableGrouping = true;

    public function __construct()
    {
        parent::__construct();
        $this->setRowIdFieldName('tag_relation_id');
    }

    public function addTagFilter($tagId)
    {
        $this->getSelect()
            ->where('tr.tag_id = ?', $tagId);
        return $this;
    }

    public function addProductFilter($productId)
    {
        $this->getSelect()
            ->where('tr.product_id = ?', $productId);
        return $this;
    }

    public function addGroupByTag()
    {
        $this->getSelect()
            ->group('tr.tag_relation_id');
        return $this;
    }

    public function addGroupByCustomer()
    {
        $this->getSelect()
            ->group('tr.customer_id');

        $this->_allowDisableGrouping = false;
        return $this;
    }

    public function resetSelect()
    {
        parent::resetSelect();
        $this->_joinFields();
        return $this;
    }

    protected function _joinFields()
    {
        $tagRelationTable = Mage::getSingleton('core/resource')->getTableName('tag/relation');
        $tagTable = Mage::getSingleton('core/resource')->getTableName('tag/tag');

        $this->addAttributeToSelect('firstname')
            ->addAttributeToSelect('lastname')
            ->addAttributeToSelect('email');

        $this->getSelect()
            ->join(array('tr' => $tagRelationTable), "tr.customer_id = e.entity_id")
            ->join(array('t' => $tagTable), "t.tag_id = tr.tag_id");
    }

    public function getSelectCountSql()
    {
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);

        if( $this->_allowDisableGrouping ) {
            $countSelect->reset(Zend_Db_Select::GROUP);
        }

        $sql = $countSelect->__toString();
        $sql = preg_replace('/^select\s+.+?\s+from\s+/is', 'select count(tr.tag_relation_id) from ', $sql);

        return $sql;
    }

}