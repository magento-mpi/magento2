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

class Mage_CustomerAlert_Model_Mysql4_Alert extends Mage_Core_Model_Mysql4_Abstract
{
   public function __construct()
   {
       $this->_init('customeralert/alert','id');
       parent::__construct();
   }
   
   
   public function updateById(Mage_Core_Model_Abstract $object, $bind, $id)
   {
       if(!isset($bind[$this->getIdFieldName()])) $bind[$this->getIdFieldName()] = $id;
       $this->getConnection('write')
                ->update($this->getMainTable(),$bind,$this->getIdFieldName().'='.$id);
   }
   
   public function save(Mage_Core_Model_Abstract $object)
   {
        $customer_id = (int)$object->getData('customer_id');
        $product_id = (int)$object->getData('product_id');
        $type = (string)$object->getData('type');
        $store_id = (int)$object->getData('store_id');
        
        $bind = array('customer_id'=>$customer_id,'product_id'=>$product_id, 'type'=>$type, 'store_id'=>$store_id);
        $read = $this->getConnection('read');
        $select = $read
            ->select()
            ->from($this->getMainTable())
            ->where('customer_id = ?', $customer_id)
            ->where('product_id = ?', $product_id)
            ->where('type = ?', $type)
            ->where('store_id = ?', $store_id);
        $row = $read->fetchOne($select);
        
        if($row>0){
            if($object->getData('checked')){
                $this->updateById($object, $bind, $row);
            } else {
                $object->setId($row);
                $object->delete();
            }
        } else {
            if($object->getData('checked')){
                parent::save($object);
            }
        }
   }
}
