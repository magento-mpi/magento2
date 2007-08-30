<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Install
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Install index controller
 *
 * @category   Mage
 * @package    Mage_Install
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */

class Mage_Install_IndexController extends Mage_Core_Controller_Front_Action
{
    function indexAction() 
    {
    	$this->_forward('begin', 'wizard', 'install');
    }
    
    function testAction()
    {
        $rule = Mage::getModel('sales/quote_rule');
        #$rule->load(1);
        if (!$rule->getId()) {
            $rule->setName('Rule 1')->setIsActive(1);
            $rule->setStartAt('2007-06-01')->setExpireAt('2007-06-30')->setCouponCode('TEST');
            $rule->setCustomerRegistered(2)->setCustomerNewBuyer(2);
            $rule->setShowInCatalog(1);
            $rule->setSortOrder(1);
            $rule->setDescription("If it is a new registered customer and it is June and coupon is TEST 
                and there's item abc123 of quantity 2 or more, make 1 item efg456 free and give 10% discount");
            
            $conditionsArr = array('type'=>'combine', 'attribute'=>'all', 'value'=>true, 'conditions'=>array(
                array('type'=>'quote_item_combine', 'attribute'=>'all', 'value'=>true, 'conditions'=>array(
                    array('type'=>'quote_item', 'attribute'=>'sku', 'operator'=>'==', 'value'=>'abc123'),
                    array('type'=>'quote_item', 'attribute'=>'qty', 'operator'=>'>=', 'value'=>2),
                )),
                array('type'=>'quote_item_combine', 'attribute'=>'any', 'value'=>false, 'conditions'=>array(
                    array('type'=>'quote_item', 'attribute'=>'sku', 'operator'=>'==', 'value'=>'efg456'),
                )),
            ));
            $rule->getConditions()->loadArray($conditionsArr);
            $actionsArr = array(
                array('type'=>'quote', 'attribute'=>'discount_percent', 'operator'=>'+=', 'value'=>10),
                array('type'=>'quote_item_add', 'value'=>'efg456', 'item_qty'=>1),
                array('type'=>'quote_item', 'attribute'=>'price', 'operator'=>'=', 'value'=>0, 'item_number'=>3, 'item_qty'=>1),
                array('type'=>'stop'),
            );
            #$rule->getActions()->loadArray($actionsArr);
            #$rule->save(); echo "SAVING...<hr>";
        }

        print_r($rule->asHtml());
    }
    
    function xmlAction()
    {
        $xml = Mage::getConfig()->getNode('global/stores/base')->asArray();
        echo "<pre>"; print_r($xml); echo "</pre>";
    }
}// Class IndexController END