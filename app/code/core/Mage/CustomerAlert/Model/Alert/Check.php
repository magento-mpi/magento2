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
 * Customer alert check model
 *
 * @category   Mage
 * @package    Mage_CustomerAlert
 * @author     Vasily Selivanov <vasily@varien.com>
 */

class Mage_CustomerAlert_Model_Alert_Check extends Mage_Core_Model_Abstract
{
    public function __construct()
    {
        $this->_init('customeralert/alert_check');
        parent::__construct();
    }
    
    public function addAlert()
    {
        $this->addData(array('status'=>'0'));
        $this->_prepareAlert();
        $this->save();
        return $this;
    }
    
    public function removeAlert()
    {
        $this->_prepareAlert();
        $this->delete();
        return $this;
    }
    
    public function addToQueue($isCustomerGroupIds)
    {
        if($isCustomerGroupIds){
            $checkIds = array();
            $rows = $this->loadByParam();
            foreach ($rows as $val) {
                $this->load($val['id']);
                $this->addData(array('status'=>1));
                $this->save();
                $checkIds[] = $val['id'];                
            }
            return $checkIds;
        } else {
            $this->_prepareAlert();
            $this->addData(array('status'=>1));
            $this->save();
            return array($this->getId());
        }
    }
    
    protected function _prepareAlert()
    {
        $data = $this->getData();
        if(!isset($data['customer_group_id']))
            $data['customer_group_id'] = '-1';
        $this->unsetData();
        $this->addData(array(
            'product_id' => $data['product_id'],
            'store_id' => $data['store_id'],
            'type' => $data['type'],
            'customer_group_id' => $data['customer_group_id'],
        ));
        $id = $this->loadByParam('fetchOne');
        $this->unsetData();
        if($id){
            $this->setId($id);
        }
        $this->addData($data);
    }
    
    public function loadByParam($fetch='fetchAll')
    {
        return $this->_getResource()->loadByParam($this, $fetch);
    }
    
    
    public function isAlertHappened()
    {
        return $this->addData(array('status'=>0))
            ->loadByParam('fetchOne');
    }
    
     public function removeAllbyAlert($alertModel)
     {
         $this->addData($alertModel->getParamValues());
         $this->_getResource()->removeByParam($this);
     }
    
    
}
