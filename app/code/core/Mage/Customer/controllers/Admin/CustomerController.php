<?php
/**
 * Customer admin controller
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_CustomerController extends Mage_Core_Controller_Admin_Action
{
    /**
     * Customers collection JSON
     *
     */
    public function gridDataAction()
    {
        $pageSize = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $collection = Mage::getResourceModel('customer','customer_collection');
        $collection->setPageSize($pageSize);
        
        
        $page = isset($_POST['start']) ? $_POST['start']/$pageSize+1 : 1;
        
        $order = isset($_POST['sort']) ? $_POST['sort'] : 'customer_id';
        $dir   = isset($_POST['dir']) ? $_POST['dir'] : 'desc';
        $collection->setOrder($order, $dir);
        $collection->setCurPage($page);
        $collection->load();
        
        //$arrGridFields = array('product_id', 'name', 'price', 'description');
        $arrGridFields = array();
        
        $this->getResponse()->setBody(Zend_Json::encode($collection->__toArray($arrGridFields)));
    }
}