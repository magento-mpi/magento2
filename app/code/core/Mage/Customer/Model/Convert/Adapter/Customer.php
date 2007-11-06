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


class Mage_Customer_Model_Convert_Adapter_Customer extends Mage_Eav_Model_Convert_Adapter_Entity
{
    public function __construct()
    {
        $this->setVar('entity_type', 'customer/customer');
    }

   /* public function load()
    {
        if (!($entityType = $this->getVar('entity_type'))
            || !(Mage::getResourceSingleton($entityType) instanceof Mage_Eav_Model_Entity_Interface)) {
            $this->addException(__('Invalid entity specified'), Varien_Convert_Exception::FATAL);
        }
        try {
            $customers = Mage::getResourceModel('customer/customer_collection');
            $customers->getEntity()
                ->setStore($this->getStoreId())
                ->addAttributeToSelect('*');
            $customers
                ->load();
            $this->addException(__('Loaded '.$collection->getSize().' records'));
        } catch (Varien_Convert_Exception $e) {
            throw $e;
        } catch (Exception $e) {
            $this->addException(__('Problem loading the collection, aborting. Error: %s', $e->getMessage()),
                Varien_Convert_Exception::FATAL);
        }
        $this->setData($collection);
        return $this;
    }*/
   
    public function load()
    {
        $attrFilterArray = array();
        $attrFilterArray ['firstname'] = 'like';
        $attrFilterArray ['lastname'] = 'like';
        $attrFilterArray ['email'] = 'like';
        $attrFilterArray ['group'] = 'eq';
        $attrFilterArray ['telephone'] = 'like';
        $attrFilterArray ['postcode'] = 'like';
        $attrFilterArray ['country'] = 'eq';
        $attrFilterArray ['region'] = 'like';
        $attrFilterArray ['created_at'] = 'dateFromTo';
        
        $attrToDb = array('group'=>'group_id');
         
        parent::setFilter($attrFilterArray,$attrToDb);
        parent::load();
    }
}