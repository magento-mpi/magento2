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
 * Customer alert helper
 *
 * @author      Vasily Selivanov <vasily@varien.com>
 */

class Mage_CustomerAlert_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getSaveAlertsUrl()
    {
         return $this->_getUrl('customeralert/Alert/saveAlerts');
    }
    
    public function isLoggedIn()
    {
        return Mage::getSingleton('customer/session')->isLoggedIn();
    }
    
    public function getAlerts($product_id)
    {
    	$customer_id = Mage::getModel('customer/session')->getId();
        
    	$res = Mage::getResourceModel('customeralert/type');
    	
    	$read = $res->getConnection('read');
            
    	$nodes = Mage::getConfig()->getNode('global/customeralert/types')->asArray();
        $alerts = array();
    	foreach ($nodes as $key=>$val ){
    	    $alerts[$key] = array('label'=>$val['label']);
    	    $select = $read
                    ->select()
                    ->from($res->getMainTable())
                    ->where('customer_id = ?', $customer_id)
                    ->where('product_id = ?', $product_id)
    	            ->where('type = ?', $key);
    	    
    	    if($read->fetchOne($select->where('type = ?', $key))>0){
    	       $alerts[$key]['checked'] = true;    
    	    } else {
    	       $alerts[$key]['checked'] = false;
    	    }
    	}
    	return $alerts;
    }
    
    public function getProductId()
    {
        return $this->_getRequest()->getParam('id');
    }
    
}
