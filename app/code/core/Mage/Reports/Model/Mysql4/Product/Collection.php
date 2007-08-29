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
 * @package    Mage_Reports
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Products Report collection
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author     Dmytro Vasylenko  <dimav@varien.com>
 */
 
class Mage_Reports_Model_Mysql4_Product_Collection extends Mage_Catalog_Model_Entity_Product_Collection
{     
    protected function _construct()
    {
        parent::__construct();
    }
    
    protected function _joinFields()
    {
        $this->_totals = new Varien_Object();
        
        $this->addAttributeToSelect('entity_id')
            ->addAttributeToSelect('name');
        $this->getSelect()->from('', array(
                    'viewed' => 'CONCAT("","")', 
                    'added' => 'CONCAT("","")',
                    'purchased' => 'CONCAT("","")',
                    'fulfilled' => 'CONCAT("","")',
                    'revenue' => 'CONCAT("","")',
                    ));
    }
    
    public function addCartsCount()
    {
        foreach ($this->getItems() as $item)
        {        
            $quotes = Mage::getResourceModel('sales/quote_item_collection')
                        ->addAttributeToFilter('product_id', $item->getId());
            $quotes->load();
            $item->setCarts($quotes->count());
        }
        return $this;
    }
    
    public function addOrdersCount()
    {
        foreach ($this->getItems() as $item)
        {        
            $quotes = Mage::getResourceModel('sales/order_item_collection')
                        ->addAttributeToFilter('product_id', $item->getId());
            $quotes->load();
            $item->setOrders($quotes->count());
        }
        return $this;
    }
    
    public function resetSelect()
    {
        parent::resetSelect();
        $this->_joinFields();
        return $this;
    }
    
    public function setOrder($attribute, $dir='desc')
    {
        switch ($attribute)
        {
            case 'viewed':
            case 'added':
            case 'purchased':
            case 'fulfilled':
            case 'revenue':
                $this->getSelect()->order($attribute . ' ' . $dir);    
                break;
            default:
                parent::setOrder($attribute, $dir);    
        }
        
        return $this;
    }
}