<?php
/**
 * Review Mysql4 resource model
 *
 * @package     Mage
 * @subpackage  Review
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Review_Model_Mysql4_Review
{
    protected $_reviewTable;
    protected $_reviewDetailTable;
    protected $_reviewStatusTable;
    protected $_reviewEntityTable;
    
    /**
     * Read connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_read;

    /**
     * Write connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_write;
    
    public function __construct() 
    {
        $this->_reviewTable         = Mage::registry('resources')->getTableName('review_resource', 'review');
        $this->_reviewDetailTable   = Mage::registry('resources')->getTableName('review_resource', 'review_detail');
        $this->_reviewStatusTable   = Mage::registry('resources')->getTableName('review_resource', 'review_status');
        $this->_reviewEntityTable   = Mage::registry('resources')->getTableName('review_resource', 'review_entity');
        
        $this->_read    = Mage::registry('resources')->getConnection('review_read');
        $this->_write   = Mage::registry('resources')->getConnection('review_write');
    }
    
    public function load($reviewId)
    {
        
    }
    
    public function save(Mage_Review_Model_Review $review)
    {
        
        $this->_write->beginTransaction();
        try {
            if ($review->getId()) {
                $data = $this->_prepareUpdateData($review);
            }
            else {
                $data = $this->_prepareInsertData($review);
                $data['base']['created_at'] = new Zend_Db_Expr('NOW()');
                $this->_write->insert($this->_reviewTable, $data['base']);
                
                $review->setReviewId($this->_write->lastInsertId());
                $data['detail']['review_id'] = $review->getId();
                $this->_write->insert($this->_reviewDetailTable, $data['detail']);
            }
            $this->_write->commit();
        }
        catch (Exception $e){
            $this->_write->rollBack();
            throw $e;
        }
    }
    
    /**
     * Prepare data for review insert
     *
     * @todo    validate data
     * @param   Mage_Review_Model_Review $review
     * @return  array
     */
    protected function _prepareInsertData(Mage_Review_Model_Review $review)
    {
        $data = array(
            'base'  => array(
                'entity_id'         => $review->getEntityId(),
                'entity_pk_value'   => $review->getEntityPkValue(),
                'status_id'         => $review->getStatusId()
            ),
            'detail'=> array(
                'title'     => strip_tags($review->getTitle()),
                'detail'    => strip_tags($review->getDetail()),
                'website_id'=> $review->getWebsiteId(),
                'nickname'  => strip_tags($review->getNickname())
            )
        );
        
        return $data;
    }
    
    public function _prepareUpdateData(Mage_Review_Model_Review $review)
    {
        
    }
    
    public function delete(Mage_Review_Model_Review $review)
    {
        
    }
}
