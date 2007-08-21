<?php
/**
 * Review Product Collection
 *
 * @package     Mage
 * @subpackage  Review
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Review_Model_Mysql4_Review_Product_Collection extends Mage_Catalog_Model_Entity_Product_Collection
{
 	protected $_entitiesAlias = array();

	public function __construct()
	{
		$this->setEntity(Mage::getResourceSingleton('catalog/product'));
        $this->setObject('catalog/product');
        $this->setRowIdFieldName('review_id');
	}

    public function addStoreFilter($storeId)
    {
        $this->getSelect()
            ->where('e.store_id = ?', $storeId);
        return $this;
    }

	public function addCustomerFilter($customerId)
	{
        $this->getSelect()
            ->where('rdt.customer_id = ?', $customerId);
		return $this;
	}

	public function addEntityFilter($entityId)
	{
        $this->getSelect()
            ->where('rt.entity_pk_value = ?', $entityId);
		return $this;
	}

	public function addStatusFilter($status)
	{
        $this->getSelect()
            ->where('rt.status_id = ?', $status);
		return $this;
	}

    public function setDateOrder($dir='DESC')
    {
        $this->getSelect()
            ->order('rt.created_at', $dir);
        return $this;
    }

    public function addReviewSummary()
    {
        foreach( $this->getItems() as $item ) {
            $model = Mage::getModel('rating/rating');
            $model->getReviewSummary($item->getReviewId());
            $item->addData($model->getData());
        }
        return $this;
    }

    public function addRateVotes()
    {
        foreach( $this->getItems() as $item ) {
            $votesCollection = Mage::getModel('rating/rating_option_vote')
                ->getResourceCollection()
                ->setEntityPkFilter($item->getEntityId())
                ->load();
            $item->setRatingVotes( $votesCollection );
        }
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
        $reviewTable = Mage::getSingleton('core/resource')->getTableName('review/review');
        $reviewDetailTable = Mage::getSingleton('core/resource')->getTableName('review/review_detail');

        $this->addAttributeToSelect('name')
            ->addAttributeToSelect('sku');

        $this->getSelect()
            ->join(array('rt' => $reviewTable), "rt.entity_pk_value = e.entity_id", array('review_id', 'created_at', 'entity_pk_value', 'status_id'))
            ->join(array('rdt' => $reviewDetailTable), "rdt.review_id = rt.review_id");
    }

    /**
     * Render SQL for retrieve product count
     *
     * @return string
     */
    public function getSelectCountSql()
    {
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);

        $sql = $countSelect->__toString();
        $sql = preg_replace('/^select\s+.+?\s+from\s+/is', 'select count(e.entity_id) from ', $sql);
        return $sql;
    }

    public function setOrder($attribute, $dir='desc')
    {
        switch( $attribute ) {
            case 'rt.review_id':
            case 'rt.created_at':
            case 'rt.status_id':
            case 'rdt.title':
            case 'rdt.nickname':
            case 'rdt.detail':
                $this->getSelect()->order($attribute . ' ' . $dir);
                break;

            default:
                parent::setOrder($attribute, $dir);
        }
        return $this;
    }

    public function addAttributeToFilter($attribute, $condition=null)
    {
        switch( $attribute ) {
            case 'rt.review_id':
            case 'rt.created_at':
            case 'rt.status_id':
            case 'rdt.title':
            case 'rdt.nickname':
            case 'rdt.detail':
                $conditionSql = $this->_getConditionSql($attribute, $condition);
                $this->getSelect()->where($conditionSql);
                return $this;
                break;

            default:
                parent::addAttributeToFilter($attribute, $condition);
        }
        return $this;
    }

 }