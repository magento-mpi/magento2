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
        $customer = Mage::getModel('customer/customer');
        Varien_Profiler::start('entity-load');
        $entity = Mage::getModel('eav/entity')->setType('customer')->setObject($customer)->load(1);
        Varien_Profiler::stop('entity-load');
        
        Varien_Profiler::start('entity-save');
        $customer->setGroup(5);
        $entity->save();
        Varien_Profiler::stop('entity-save');
        
        echo '<pre>';
        print_r($customer->getData());
        echo '</pre>';
        
        Varien_Profiler::start('entity-collection');
        $collection = Mage::getModel('eav/entity_collection')->setEntity($entity)
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('firstname', array('like'=>'dmit%'))
            ->addAttributeToSort('lastname')
            ->setPage(1,10)
            ->load();
        Varien_Profiler::stop('entity-collection');
        echo '<pre>';
        print_r($collection->exportToArray());
        echo '</pre>';
        
        echo $this->getLayout()->createBlock('core/profiler')->toHtml();
    }
}
