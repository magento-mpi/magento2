<?php
/**
 * Install index controller
 *
 * @package     Mage
 * @subpackage  Install
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */

class Mage_Install_IndexController extends Mage_Core_Controller_Front_Action
{
    function indexAction() 
    {
        $this->_forward('begin', 'wizard', 'Mage_Install');
    }
    
    function testAction()
    {
        $rule = Mage::getModel('sales', 'quote_rule');
        $rule->setName('Rule 1');
        $rule->setDescription("If it is a new registered customer and coupon is TEST and there's item abc123 of quantity 2 or more, make 1 item efg456 free and give 10% discount");
        
        $conditionsArr = array('type'=>'combine', 'attribute'=>'all', 'operator'=>'=', 'value'=>true, 'conditions'=>array(
            array('type'=>'system', 'attribute'=>'date', 'operator'=>'>=', 'value'=>'2007-06-01'),
            array('type'=>'system', 'attribute'=>'date', 'operator'=>'<=', 'value'=>'2007-07-30'),
            array('type'=>'quote', 'attribute'=>'coupon_code', 'operator'=>'=', 'value'=>'TEST'),
            array('type'=>'customer', 'attribute'=>'registered', 'operator'=>'=', 'value'=>'TRUE'),
            array('type'=>'customer', 'attribute'=>'first_time_buyer', 'operator'=>'=', 'value'=>'TRUE'),
            array('type'=>'quote_item_combine', 'attribute'=>'all', 'operator'=>'=', 'value'=>true, 'conditions'=>array(
                array('type'=>'quote_item', 'attribute'=>'sku', 'operator'=>'=', 'value'=>'abc123'),
                array('type'=>'quote_item', 'attribute'=>'qty', 'operator'=>'>=', 'value'=>2),
            )),
            array('type'=>'quote_item_combine', 'attribute'=>'all', 'operator'=>'=', 'value'=>true, 'conditions'=>array(
                array('type'=>'quote_item', 'attribute'=>'sku', 'operator'=>'=', 'value'=>'efg456'),
            )),
        ));
        $rule->getConditions()->loadArray($conditionsArr);

        echo "<pre>"; print_r($rule->toString()); echo "</pre>";
    }
}// Class IndexController END