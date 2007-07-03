<?php
/**
 * Newsletter subscriber model for MySQL4 
 *
 * @package     Mage
 * @subpackage  Newsletter
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Newsletter_Model_Mysql4_Subscriber
{
    /**
     * DB read connection
     * 
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_read;
    
    /**
     * DB write connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_write;
    
    /**
     * Name of subscriber DB table
     * 
     * @var string
     */
    protected $_subscriberTable;
    
    /**
     * Constructor
     *
     * Set read and write connection, get tablename from config
     */
    public function __construct() 
    {
        $this->_subscriberTable = Mage::getSingleton('core/resource')->getTableName("newsletter/subscriber");
        $this->_read = Mage::getSingleton('core/resource')->getConnection('newsletter_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('newsletter_write');
    }
    
    /**
     * Load subscriber from DB
     *
     * @param int $subscriberId
     * @return array
     */
    public function load($subscriberId)
    {
        $select = $this->_read->select()
            ->from($this->_subscriberTable)
            ->where($this->_read->quoteInto('subscriber_id=?',$subscriberId));
        
        return $this->_read->fetchRow($select);
    }
    
    /**
     * Save subscriber info from it model.
     *
     * @param  Mage_Newsletter_Model_Subscriber $subscriber
     * @return Mage_Newsletter_Model_Subscriber
     */
    public function save(Mage_Newsletter_Model_Subscriber $subscriber)
    {
        $this->_write->beginTransaction();
        try {
            $data = $this->_prepareSave($subscriber);
            if ($subscriber->getId()) {
                $this->_write->update($this->_subscriberTable, $data,
                                      $this->_write->quoteInto('subscriber_id=?',$subscriber->getId()));
            } else {
                if(!$subscriber->getCustomerId()) {
                    $data['subscriber_confirm_code'] = $this->_generateRandomCode();
                    $data['status'] = Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE;
                    $subscriber->setStatus(Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE);
                }
                $this->_write->insert($this->_subscriberTable, $data);
                $subscriber->setId($this->_write->lastInsertId($this->_subscriberTable));
            }            
            $this->_write->commit();
        }
        catch(Exception $e) {
            $this->_write->rollBack();
            Mage::throwException('cannot save you subscription');
        }
        
        return $subscriber;
    }
    
    /**
     * Generates random code for subscription confirmation
     *
     * @return string 
     */
    protected function _generateRandomCode()
    {
        return md5(microtime() + rand());
    }
    
    /**
     * Preapares data for saving of subscriber
     *
     * @param  Mage_Newsletter_Model_Subscriber $subscriber
     * @return array
     */
    protected function _prepareSave(Mage_Newsletter_Model_Subscriber $subscriber) 
    {
        $data = array();
        $data['customer_id'] = $subscriber->getCustomerId();
        $data['subscriber_status'] = $subscriber->getStatus();
        $data['subscriber_email']  = $subscriber->getEmail();
        $data['subscriber_confirm_code'] = $subscriber->getCode();
        
        $validators = array('subscriber_email' => 'EmailAddress');
        $filters = array();
        $input = new Zend_Filter_Input($filters, $validators, $data);
        if ($input->hasInvalid() || $input->hasMissing()) {
            $resultInvalidMessage = '';
            foreach ($input->getMessages() as $message) {
                $resultInvalidMessage.= $message . "\n";
            }
            Mage::throwException($resultInvalidMessage);
        }
        
        return $data;
    }
    
    /**
     * Delete subscriber from DB
     *
     * @param int $subscriberId
     */
    public function delete($subscriberId) 
    {
        if(!(int)$subscriberId) {
            Mage::throwException('Ivalid subscriber id');
        }
        
        $this->_write->beginTransaction();
        try {
            $this->_write->delete($this->_subscriberTable, 
                                  $this->_write->quoteInto('subscriber_id=?', $subscriberId));
            $this->_write->commit();
        }
        catch (Exception $e) {
            $this->_write->rollBack();
            Mage::throwException('Cannot delete subscriber');
        }
    }
}