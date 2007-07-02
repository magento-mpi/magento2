<?php
/**
 * Newsletter Subscribers Collection
 * 
 * @package     Mage
 * @subpackage  Newsletter
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Newsletter_Model_Mysql4_Subscriber_Collection extends Varien_Data_Collection_Db
{   
    /**
     * Subscribers table name
     *
     * @var string
     */
    protected $_subscriberTable;
    
     /**
     * Queue link table name
     *
     * @var string
     */
    protected $_queueLinkTable;
    
    /**
     * Constructor
     *
     * Configures collection
     */
    public function __construct() 
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('newsletter_read'));
        $this->_subscriberTable = Mage::getSingleton('core/resource')->getTableName('newsletter/subscriber');
        $this->_queueLinkTable = Mage::getSingleton('core/resource')->getTableName('newsletter/queue_link');
        $this->_sqlSelect->from($this->_subscriberTable);
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('newsletter/subscriber'));
    }
    
    /**
     * Load subscribers by queue
     * 
     * @param Mage_Newsletter_Model_Queue $queue
     */
    public function useQueue(Mage_Newsletter_Model_Queue $queue)
    {
        $this->_sqlSelect->join($this->_queueLinkTable, "{$this->_queueLinkTable}.subscriber_id = {$this->_subscriberTable}.subscriber_id")
            ->where(new Zend_Db_Expression('{$this->_queueLinkTable}.queue_id = ' . intval($queue->getId()));
            
        return $this;
    }
}