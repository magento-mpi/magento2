<?php
/**
 * Newsletter queue model.
 * 
 * @package     Mage
 * @subpackage  Newsletter
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */ 
class Mage_Newsletter_Model_Queue extends Mage_Core_Model_Abstract
{
    /**
     * Subscribers collection
     * @var Varien_Data_Collection_Db
     */
    
    protected $_subscribersCollection = null;
    
        
    protected function _construct() 
    {
        $this->_init('newsletter/queue');
    }
    
    /**
     * Returns subscribers collection for this queue
     *
     * @return Varien_Data_Collection_Db
     */
    public function getSubscribersCollection()
    {
        if (is_null($this->_subscribersCollection)) {
            $this->_subscribersCollection = Mage::getResourceModel('newsletter/subscriber_collection')
                ->useQueue($this);
        }
        
        return $this->_subscribersCollection;
    }
    
    public function addTemplateData( $data ) 
    {
        if ($data->getTemplateId()) {
            $this->setTemplate(Mage::getModel('newsletter/template')
                                    ->load($data->getTemplateId()));
        }

        return $this;
    }
    
    public function sendPerSubscriber($count=20) 
    {
        $collection = $this->getSubscribersCollection()
            ->useOnlyUnsent()
            ->setPageSize($count)
            ->setCurPage(1)
            ->load();
            
        foreach($collection->getItems() as $item) {
            print_r($item);
        }
    }
}