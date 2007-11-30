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
 * Customer alert form block
 *
 * @category   Mage
 * @package    Mage_CustomerAlert
 * @author     Vasily Selivanov <vasily@varien.com>
 */
class Mage_CustomerAlert_Block_Alerts extends Mage_Core_Block_Template
{
    protected $_alertType;
    
    public function toHtml()
    {
        $template = Mage::getModel('customeralert/config')->getTemplateName($this->_alertType);
        if($template) {
            $this->setTemplate('customeralert/'.$template.'.phtml');
        }
        return parent::toHtml();
    }
    
    public function setAlertType($alertType)
    {
        $this->_alertType = $alertType;
        return $this;
    }
    
    public function getAlertType()
    {
        return $this->_alertType;
    }
    
    public function getAlertLabel()
    {
        $alert = Mage::getModel('customeralert/config')->getAlerts();
        if(isset($alert[$this->_alertType])){
            return $alert[$this->_alertType]['label'];             
        }
    }
    
    public function isCustomerSubscribed()
    {
        $data = array(
           'product_id'  => $this->helper('customerAlert')->getProductId(),
           'customer_id' => Mage::getModel('customer/session')->getId(),
           'store_id'    => Mage::app()->getStore()->getId(),
        
        );
        
        return Mage::getModel('customeralert/config')->getAlertByType($this->_alertType)
            ->addData($data)
            ->loadByParam()
            ->isChecked();
            
    }
}