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
 * @package    Mage_Cms
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer alert type model
 *
 * @category   Mage
 * @package    Mage_CustomerAlert
 * @author     Vasily Selivanov <vasily@varien.com>
 */

abstract  class Mage_CustomerAlert_Model_Type extends Mage_Core_Model_Abstract
{
    protected $_userCheck;
    
    protected $_oldValue;
    protected $_newValue;
    protected $_date;
    
    public function __construct()
    {
        $this->_init('customeralert/type', 'id');
    }

    public function loadByParam()
    {
        $data = $this->getResource()->loadByParam($this);
        if(isset($data[0])){
            $this->addData($data[0]);
        }
        return $this; 
    }
    
    public function isChecked()
    {
        return ($this->getId()) ? true : false;
    }
    
    public function getAlertHappened()
    {
        return $this->_alertHappen = Mage::getModel('customeralert/alert_check')
            ->addData($this->getData())
            ->isAlertHappened();
    }
    
    public function save()
    {
        $this->loadByParam();
        if($this->_userCheck) {
            parent::save();
        } else {
            if($this->isChecked()){
                $this->delete();    
            }
        }
    }
    
    /*public function addToQueue()
    {
        $res=Mage::getResourceModel('customeralert/queue');
        $mod=Mage::getModel('customeralert/queue');
        $res->addSubscribersToQueue($mod,array($this->getCustomerId()));    
    }*/
    
    
    public function checkByUser($value)
    {
        $this->_userCheck = ($value=='true') ? true : false;
        return $this;
    }
    
    public function addAlert($check, $newValue = null, $oldValue = null)
    {
        $alertCheck = Mage::getModel('customeralert/alert_check')
                ->addData($this->getData())
                ->addData(array('new_value'=>$newValue,'old_value'=>$oldValue,'date'=>now()));     
        if($check) {
            $alertCheck->addAlert();
        } else {
            $alertCheck->removeAlert();
        }
        return $this;
    }
    
    public function getAlertChangedValues()
    {
        $values = Mage::getModel('customeralert/alert_check')
            ->addData(array(
                'product_id'=>$this->getData('product_id'),
                'store_id'=>$this->getData('store_id'),
                'type'=>$this->getData('type'),
            ))
            ->loadByParam();
        return $values[0];
    }
    
    public function addCustomersToAlertQueue()
    {
        
    }
    
    public function getAlertText()
    {   
        if($this->getAlertHappened()){
            $changedValues = $this->getAlertChangedValues();
            $this->_oldValue = $changedValues['old_value'];
            $this->_newValue = $changedValues['new_value'];
            $this->_date = $changedValues['date'];
            return $this->getAlertHappenedText(); 
        } else {
            return $this->getAlertNotHappenedText();            
        }
        
    }
    
    abstract public function getAlertHappenedText();
    abstract public function getAlertNotHappenedText();
    abstract public function checkBefore(Mage_Catalog_Model_Product $oldProduct, Mage_Catalog_Model_Product $newProduct);
    abstract public function checkAfter(Mage_Catalog_Model_Product $oldProduct, Mage_Catalog_Model_Product $newProduct);
    
}
