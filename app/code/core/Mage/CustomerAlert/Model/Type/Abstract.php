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

abstract class Mage_CustomerAlert_Model_Type_Abstract extends Mage_Core_Model_Abstract
{
    protected $_oldValue;
    protected $_newValue;
    protected $_date;
    
    public function __construct()
    {
        $this->_init('customeralert/type');
    }
    
    public function loadCustomersId()
    {
        $rows = $this->getResource()
            ->loadIds($this->getProductId(), $this->getStoreId(), $this->type ,'fetchAll');
        $customersId = array();
        foreach ($rows as $val){
            $customersId[] = $val['customer_id'];
        }
        return $customersId;
    }
    
    public function addToQueue()
    {
        $res=Mage::getResourceModel('customeralert/queue');
        $mod=Mage::getModel('customeralert/queue');
        $res->addSubscribersToQueue($mod,array($this->getCustomerId()));    
    }

    
    public function check()
    {
        $row = Mage::getModel('customerAlert/alert_check')
            ->set($this->getProductId(), $this->getStoreId, $this->type)
            ->loadIds('fetchAll');
        if(count($row)>0){
            $this->setData('checked',true);
            $row = $row[0];
            $this->_oldValue = $row['old_value'];
            $this->_newValue = $row['new_value'];
            $this->_date = $row['date'];
        } else {
            $this->setData('checked',false);
        }
        
        return $this;
    }
    
    public function setChecked($check, $newValue = null, $oldValue = null)
    {
         
        $mod = Mage::getModel('customerAlert/alert_check')
                 ->set($this->getProductId(), $this->getStoreId, $this->type);
        if($check) {
            $this->_newValue = $newValue;
            $this->_oldValue = $oldValue;
            $this->_date = now();
            $mod->setChecked($this->_newValue, $this->_oldValue, $this->_date);
            $this->setData('checked',true);
        } else {
            $this->setData('checked',false);
            $mod->removeChecked();
        }
    }
    
    public function getDate()
    {
        return $this->_date;
    }
    abstract public function getCheckedText();
    abstract public function checkBefore(Mage_Catalog_Model_Product $oldProduct, Mage_Catalog_Model_Product $newProduct);
    abstract public function checkAfter(Mage_Catalog_Model_Product $oldProduct, Mage_Catalog_Model_Product $newProduct);
    
    
    
}
