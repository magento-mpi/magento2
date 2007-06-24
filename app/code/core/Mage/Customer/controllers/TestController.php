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
        Varien_Profiler::start('entity');
        $customer = Mage::getModel('customer/entity');
        $customer->load(1);
        echo '<pre>';
        print_r($customer->getData());
        echo '</pre>';
        Varien_Profiler::stop('entity');
        echo '<pre>';
        print_r(Varien_Profiler::fetch('entity'));
        echo '</pre>';
    }
}
