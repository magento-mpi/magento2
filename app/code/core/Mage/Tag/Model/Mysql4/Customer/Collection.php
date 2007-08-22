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
    protected $_countAttribute = 'tr.tag_relation_id';

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

    public function addStatusFilter($status)
    {
        $this->getSelect()
            ->where('t.status = ?', $status);
        return $this;
    }

    public function addDescOrder()
    {
        $this->getSelect()
            ->order('tr.tag_relation_id desc');
        return $this;
    }

    public function addGroupByTag()
    {
        $this->getSelect()
            ->group('tr.tag_relation_id');

        $this->_allowDisableGrouping = false;
        return $this;
    }

    public function addGroupByCustomer()
    {
        $this->getSelect()
            ->group('tr.customer_id');

        $this->_allowDisableGrouping = false;
        return $this;
    }

    public function addCustomerFilter($customerId)
    {
        $this->getSelect()
            ->where('tr.customer_id = ?', $customerId);
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
        $sql = preg_replace('/^select\s+.+?\s+from\s+/is', "select count({$this->getCountAttribute()}) from ", $sql);
        return $sql;
    }

    public function addProductName()
    {
        $productsId = array();
        $productsData = array();

        foreach ($this->getItems() as $item)
        {
            $productsId[] = $item->getProductId();
        }

        $productsId = array_unique($productsId);

        /* small fix */
        if( sizeof($productsId) == 0 ) {
            return;
        }

        $collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addIdFilter($productsId);

        $collection->getEntity()->setStore(0);
        $collection->load();

        foreach ($collection->getItems() as $item)
        {
            $productsData[$item->getId()] = $item->getName();
            $productsSku[$item->getId()] = $item->getSku();
        }

        foreach ($this->getItems() as $item)
        {
            $item->setProduct($productsData[$item->getProductId()]);
            $item->setProductSku($productsSku[$item->getProductId()]);
        }
        return $this;
    }

    public function setOrder($attribute, $dir='desc')
    {
        switch( $attribute ) {
            case 'name':
            case 'status':
                $this->getSelect()->order($attribute . ' ' . $dir);
                break;

            default:
                parent::setOrder($attribute, $dir);
        }
        return $this;
    }

    public function setCountAttribute($value)
    {
        $this->_countAttribute = $value;
        return $this;
    }

    public function getCountAttribute()
    {
        return $this->_countAttribute;
    }
}