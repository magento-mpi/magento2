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
 * @package    Mage_CustomerAlert
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
/**
 * Customer Alerts module observer
 *
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_CustomerAlert_Model_Observer
{
    protected $_oldProduct;
    
    
    public function catalogProductSaveBefore($observer)
    {
        $newProduct = $observer->getEvent()->getProduct();
        $product_id = $newProduct->getId();
        
        $oldProduct = Mage::getModel('catalog/product')
                        ->load($product_id);
        
        $this->_oldProduct = $oldProduct;
        
        $res = Mage::getResourceModel('customeralert/type');
        $rows = $res->loadIds($product_id,$newProduct->getType());
        
        foreach ($rows as $row) {
            $mod = Mage::getModel(Mage::getConfig()->getNode('global/customeralert/types/'.$row['type'].'/model'))
                ->load($row['id']);
            $mod->checkBefore($oldProduct,$newProduct);
        }
    }
    
    public function catalogProductSaveAfter($observer)
    {
        $newProduct = $observer->getEvent()->getProduct();
        $product_id = $newProduct->getId();
        $oldProduct = Mage::getModel('catalog/product')
                        ->load($product_id);
        $res = Mage::getResourceModel('customeralert/type');
        $rows = $res->loadIds($product_id,$newProduct->getType());
        foreach ($rows as $row) {
            $mod = Mage::getModel(Mage::getConfig()->getNode('global/customeralert/types/'.$row['type'].'/model'))
                ->load($row['id']);
            $mod->checkAfter($this->_oldProduct,$newProduct);
        }
    }
}
