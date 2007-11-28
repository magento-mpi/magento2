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

class Mage_CustomerAlert_Model_Type extends Mage_Core_Model_Abstract
{
    protected $_userCheck;
    
    protected $_oldValue;
    protected $_newValue;
    protected $_date;
    
    public function __construct()
    {
        $this->_init('customeralert/type', 'id');
    }
    
    public function getParamValues()
    {
        $value = array();
        if($this->getData('product_id')){
            $value['product_id'] = $this->getData('product_id');
        }
        
        if($this->getData('store_id')){
            $value['store_id'] = $this->getData('store_id');
        }
        
        if($this->getData('type')){
            $value['type'] = $this->getData('type');
        }
        return $value;
    }
    
    public function setParamValues($data)
    {
        $this->setData(array(
            'product_id' => $data['product_id'],
            'store_id' => $data['store_id'],
        ));
        return $this;
    }
    public function loadAllByParam()
    {
        return $this->getResource()->loadByParam($this);
    }
    
    public function loadByParam()
    {
        $data = $this->loadAllByParam();
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
            ->addData($this->getParamValues())
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
    
    public function checkByUser($value)
    {
        $this->_userCheck = ($value=='true') ? true : false;
        return $this;
    }
    
    public function addAlert($check, $newValue = null, $oldValue = null)
    {
        $alertCheck = Mage::getModel('customeralert/alert_check')
                ->addData($this->getParamValues())
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
            ->addData($this->getParamValues())
            ->loadByParam();
        return $values[0];
    }
    
    public function addCustomersToAlertQueue()
    {
        if($this->getAlertHappened())
        {
            $rows = $this->loadAllByParam();
            $customersId = array();
            foreach ($rows as $row){
                $customersId[] = $row['customer_id'];
            }
            Mage::getModel('customeralert/queue')
                ->addSubscribersToQueue($customersId);
        }
    }
}
