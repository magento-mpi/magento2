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
        Varien_Profiler::start('entity-load');
        $customer = Mage::getModel('customer/entity')->load(1);
        Varien_Profiler::stop('entity-load');
        
        echo '<pre>';
        print_r($customer->getData());
        echo '</pre>';
        
        Varien_Profiler::start('entity-collection');
        $collection = $customer->getEmptyCollection()
            ->addAttributeSelect('firstname')
            ->addAttributeSelect('lastname')
            ->addAttributeFilter('lastname', 'soroka')
            ->load(true);
        Varien_Profiler::stop('entity-collection');
        echo '<pre>';
        print_r($collection->getItems());
        echo '</pre>';
        
        echo $this->getLayout()->createBlock('core/profiler')->toHtml();
    }
}
