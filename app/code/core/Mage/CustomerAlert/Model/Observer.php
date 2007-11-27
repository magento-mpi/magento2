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
    
    protected function _getIdsForCheck($data){
        return $rows = Mage::getModel('customeralert/type')
                ->addData($data)
                ->loadByParam();
    }
    
    protected function _getData($newProduct)
    {
        $data = array(
            'product_id' => $newProduct->getId(),
            'store_id'   => $newProduct->getStoreId(), 
        ); 
        return $data;
    }
    
    public function catalogProductSaveBefore($observer)
    {
        $newProduct = $observer->getEvent()->getProduct();
        $data = $this->_getData($newProduct);
        $this->_oldProduct = Mage::getModel('catalog/product')->load($data['product_id']);
        $rows = $this->_getIdsForCheck($data);
        foreach ($rows as $row) {
            Mage::getModel('customeralert/config')->getAlertByType($row['type'])
                ->addData($data)
                ->checkBefore($this->_oldProduct,$newProduct);
        }
    }
    
    public function catalogProductSaveAfter($observer)
    {
        $data = array();
        $newProduct = $observer->getEvent()->getProduct();
        $data = $this->_getData($newProduct);
        $rows = $this->_getIdsForCheck($data);
        foreach ($rows as $row) {
            Mage::getModel('customeralert/config')->getAlertByType($row['type'])
                ->addData($data)
                ->checkAfter($this->_oldProduct,$newProduct);
        }
    }
}
