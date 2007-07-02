<?php
/**
 * test
 *
 * @package     Core
 * @subpackage  Core
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Customer_TestController extends Mage_Core_Controller_Front_Action 
{
    public function indexAction()
    {
        Varien_Profiler::start('entity-init');
        $entity = Mage::getModel('eav/entity')->setType('customer')->loadAllAttributes();
        Varien_Profiler::stop('entity-init');

        $customer = Mage::getModel('customer/customer');

        Varien_Profiler::start('entity-save');
        $customer->setFirstname('Moshe')->setLastname('Gurvich')->setEmail('moshe@varien.com')
            ->setCustomerGroup(1)->setStoreBalance(12.34)
            ->setCreatedAt(now())->setUpdatedAt(now());
        $entity->save($customer);
        Varien_Profiler::stop('entity-save');
        
        $customerId = $customer->getEntityId();
        
        $customer = Mage::getModel('customer/customer');
        
        Varien_Profiler::start('entity-load');
        $entity->load($customer, $customerId);
        Varien_Profiler::stop('entity-load');

        echo '<pre>';
        print_r($customer->getData());
        echo '</pre>';
        
        Varien_Profiler::start('entity-collection');
        $collection = Mage::getModel('eav/entity_collection')
            ->setEntity($entity)->setObject($customer)
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('firstname', array('like'=>'mos%'))
            ->addAttributeToSort('lastname')
            ->setPage(1,10)
            ->load();
        Varien_Profiler::stop('entity-collection');

        echo '<pre>';
        print_r($collection->exportToArray());
        echo '</pre>';
        
        
        Varien_Profiler::start('entity-delete');
        #$collection->delete();
        Varien_Profiler::stop('entity-delete');

        echo $this->getLayout()->createBlock('core/profiler')->toHtml();
    }
}
