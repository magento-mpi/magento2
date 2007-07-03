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
class Mage_Newsletter_Model_Queue extends Varien_Object
{
    // TODO: Realize model... talk with Dmiriy about best solution
    
    
    public function load($queueId) 
    {
        
    }
    
    /**
     * Returns subscribers collection for this queue
     *
     * @return Mage_Newsletter_Model_Mysql4_Subscriber_Collection
     */
    public function getSubscribersCollection()
    {
        return Mage::getResourceSingleton('newsletter/subscriber_collection')
            ->useQueue($this);
    }
    
    public function addTemplateData( $data ) 
    {
        if($data->getTemplateId()) {
            $this->setTemplate(Mage::getModel('newsletter/template')
                                    ->load($data->getTemplateId()));
        }        
        return $this;
    }
    
    public function save()
    {
        return $this;
    }
    
    public function delete()
    {
        return $this;
    }
    
    
}