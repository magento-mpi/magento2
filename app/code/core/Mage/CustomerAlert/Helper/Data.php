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

class Mage_CustomerAlert_Helper_Data extends Mage_Core_Helper_Url
{
    public function getSaveAlertsUrl($type)
    {
        return $this->_getUrl('customeralert/alert/savealerts',
            array(
                'product_id' => $this->getProductId(),
                'type' => $type,
                Mage_Core_Controller_Front_Action::PARAM_NAME_BASE64_URL  => $this->getCurrentBase64Url() 
                )
            );
    }
    
    public function getAlerts()
    {
    	$data = array(
    	   'product_id'  => $this->getProductId(),
    	   'customer_id' => Mage::getModel('customer/session')->getId(),
    	   'store_id'    => Mage::app()->getStore()->getId(),
    	
    	);
        $nodes = Mage::getSingleton('customeralert/config')->getAlerts();
        foreach ($nodes as $key=>$val ){
            $alerts[$key] = array('label'=>$val['label']);
            $alerts[$key]['checked'] = Mage::getSingleton('customeralert/config')
                ->getAlertByType($key)
                ->addData($data)
                ->loadByParam()
                ->isChecked();
        }
        return $alerts;
    }
    
    public function getProductId()
    {
        return $this->_getRequest()->getParam('id');
    }
    
}
