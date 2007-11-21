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
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer alert controller
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Vasily Selivanov <vasily@varien.com>
 */
class Mage_CustomerAlert_AlertController extends Mage_Core_Controller_Front_Action
{
   
   public function saveAlertsAction()
     {
         $customer_id = Mage::getModel('customer/session')->getId();
         if($customer_id){
             $params = $this->getRequest()->getParams();
             if(isset($params['_product_id'])){
                 $product_id = $params['_product_id'];
                 unset($params['_product_id']);
                 foreach ($params as $key => $val){
                     Mage::getModel(Mage::getConfig()->getNode('global/customeralert/types/'.$key.'/model'))
                         ->setCustomerId($customer_id)
                         ->setProductId($product_id)
                         ->setChecked($val)
                         ->save();
                }
            }
         }
     }
}