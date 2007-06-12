<?php
/**
 * Review collection resource model
 *
 * @package     Mage
 * @subpackage  Review
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Review_Model_Mysql4_Review_Collection extends Varien_Data_Collection_Db 
{
    protected $_reviewTable;
    protected $_reviewDetailTable;
    protected $_reviewStatusTable;
    protected $_reviewEntityTable;
    
    public function __construct() 
    {
        parent::__construct(Mage::registry('resources')->getConnection('review_read'));
        
        $this->_reviewTable         = Mage::registry('resources')->getTableName('review_resource', 'review');
        $this->_reviewDetailTable   = Mage::registry('resources')->getTableName('review_resource', 'review_detail');
        $this->_reviewStatusTable   = Mage::registry('resources')->getTableName('review_resource', 'review_status');
        $this->_reviewEntityTable   = Mage::registry('resources')->getTableName('review_resource', 'review_entity');
        
        $this->_sqlSelect->from($this->_reviewTable)
            ->join($this->_reviewDetailTable, $this->_reviewTable.'.review_id='.$this->_reviewDetailTable.'.review_id');
        
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('review/review'));
    }
    
    /**
     * Add website filter
     *
     * @param   int $websiteId
     * @return  Varien_Data_Collection_Db
     */
    public function addWebsiteFilter($websiteId)
    {
        Mage::log('Add website filter to review collection');
        $this->addFilter('website', 
            $this->getConnection()->quoteInto($this->_reviewDetailTable.'.website_id=?', $websiteId), 
            'string');
            
        return $this;
    }
    
    /**
     * Add entity filter
     *
     * @param   int|string $entity
     * @param   int $pkValue
     * @return  Varien_Data_Collection_Db
     */
    public function addEntityFilter($entity, $pkValue)
    {
        Mage::log('Add entity filter to review collection');
        if (is_numeric($entity)) {
            $this->addFilter('entity', 
                $this->getConnection()->quoteInto($this->_reviewTable.'.entity_id=?', $entity),
                'string');
        }
        elseif (is_string($entity)) {
            $this->_sqlSelect->join($this->_reviewEntityTable,
                $this->_reviewTable.'.entity_id='.$this->_reviewEntityTable.'.entity_id');
                
            $this->addFilter('entity', 
                $this->getConnection()->quoteInto($this->_reviewEntityTable.'.entity_code=?', $entity), 
                'string');
        }

        $this->addFilter('entity_pk_value', 
            $this->getConnection()->quoteInto($this->_reviewTable.'.entity_pk_value=?', $pkValue),
            'string');
            
        return $this;
    }
    
    /**
     * Add status filter
     *
     * @param   int|string $status
     * @return  Varien_Data_Collection_Db
     */
    public function addStatusFilter($status)
    {
        Mage::log('Add status filter to review collection');
        if (is_numeric($status)) {
            $this->addFilter('status', 
                $this->getConnection()->quoteInto($this->_reviewTable.'.status_id=?', $status), 
                'string');
        }
        elseif (is_string($status)) {
            $this->_sqlSelect->join($this->_reviewStatusTable,
                $this->_reviewTable.'.status_id='.$this->_reviewStatusTable.'.status_id');
                
            $this->addFilter('status', 
                $this->getConnection()->quoteInto($this->_reviewStatusTable.'.status_code=?', $status), 
                'string');
        }
            
        return $this;
    }
    
    public function setDateOrder($dir='DESC')
    {
        $this->setOrder('created_at', $dir);
        return $this;
    }
}
