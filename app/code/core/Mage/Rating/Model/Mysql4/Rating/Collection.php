<?php
/**
 * Rating collection resource model
 *
 * @package     Mage
 * @subpackage  Rating
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Rating_Model_Mysql4_Rating_Collection extends Varien_Data_Collection_Db 
{
    protected $_ratingTable;
    protected $_ratingEntityTable;
    protected $_ratingOptionTable;
    protected $_ratingVoteTable;
    
    public function __construct() 
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('rating_read'));
        
        $this->_ratingTable         = Mage::getSingleton('core/resource')->getTableName('rating/rating');
        $this->_ratingEntityTable   = Mage::getSingleton('core/resource')->getTableName('rating/rating_entity');
        $this->_ratingOptionTable   = Mage::getSingleton('core/resource')->getTableName('rating/rating_option');
        $this->_ratingVoteTable     = Mage::getSingleton('core/resource')->getTableName('rating/rating_vote');
        
        $this->_sqlSelect->from($this->_ratingTable);
        
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('rating/rating'));
    }
    
    /**
     * add entity filter
     *
     * @param   int|string $entity
     * @return  Varien_Data_Collection_Db
     */
    public function addEntityFilter($entity)
    {
        if (is_numeric($entity)) {
            
        }
        elseif (is_string($entity)) {
        	$this->_sqlSelect->join($this->_ratingEntityTable, 
        	   $this->_ratingTable.'.entity_id='.$this->_ratingEntityTable.'.entity_id');
            
            $this->addFilter('entity',
                $this->getConnection()->quoteInto($this->_ratingEntityTable.'.entity_code=?', $entity),
                'string');
        }
        return $this;
    }
    
    /**
     * set order by position field
     *
     * @param   string $dir
     * @return  Varien_Data_Collection_Db
     */
    public function setPositionOrder($dir='ASC')
    {
        $this->setOrder($this->_ratingTable.'.position', $dir);
        return $this;
    }
    
    /**
     * add options to ratings in collection
     *
     * @return Varien_Data_Collection_Db
     */
    public function addOptionToItems()
    {
        $arrRatingId = $this->getColumnValues('rating_id');

        if (!empty($arrRatingId)) {
            $collection = Mage::getResourceModel('rating/rating_option_collection')
                ->addRatingFilter($arrRatingId)
                ->setPositionOrder()
                ->load();
            
            foreach ($this as $rating) {
            	$rating->setOptions($collection->getItemsByColumnValue('rating_id', $rating->getId()));
            }
        }
        
        return $this;
    }
    
    public function addEntitySummaryToItem($entityPkValue)
    {
        $arrRatingId = $this->getColumnValues('rating_id');
        $sql = "SELECT
                    $this->_ratingOptionTable.rating_id as rating_id,
                    SUM($this->_ratingOptionTable.value) as sum,
                    COUNT(*) as count
                FROM
                    $this->_ratingOptionTable,
                    $this->_ratingVoteTable
                WHERE
                    $this->_ratingOptionTable.option_id=$this->_ratingVoteTable.option_id
                    AND ".$this->getConnection()->quoteInto($this->_ratingOptionTable.'.rating_id IN (?)', $arrRatingId)."
                    AND ".$this->getConnection()->quoteInto($this->_ratingVoteTable.'.entity_pk_value=?', $entityPkValue)."
                GROUP BY
                    $this->_ratingOptionTable.rating_id";
        
        $data = $this->getConnection()->fetchAll($sql);
        
        foreach ($data as $item) {
        	$rating = $this->getItemById($item['rating_id']);
        	if ($rating && $item['count']>0) {
        	    $rating->setSummary($item['sum']/$item['count']);
        	}
        }
        return $this;
    }
}
