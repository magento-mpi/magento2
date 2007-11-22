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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Alerts products admin grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Vasily Selivanov <vasily@varien.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Alerts extends Mage_Core_Block_Template 
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/product/tab/alert.phtml');
    }
    
    public function getAlerts()
    {
    	$res = Mage::getResourceModel('customeralert/type');
    	$read = $res->getConnection('read');
    	$nodes = Mage::getConfig()->getNode('global/customeralert/types')->asArray();
        $alerts = array();
    	foreach ($nodes as $key=>$val ){
    	    $alerts[$key] = array('label'=>$val['label']);
    	}
    	return $alerts;	
    }   
    

    protected function _prepareLayout()
    {
        $params = $this->getRequest()->getParams();
        $product_id = $params['id'];
        $storeId = $params['store'];
        $accordion = $this->getLayout()->createBlock('adminhtml/widget_accordion')
            ->setId('AlertsBlockId');
        foreach ($this->getAlerts() as $key=>$val) {
           $customerIds = Mage::getModel(Mage::getConfig()->getNode('global/customeralert/types/'.$key.'/model'))
                         ->setProductId($product_id)
                         ->setStoreId($storeId)
                         ->loadCustomersId();
           $accordion->addItem($key, array(
	            'title'     => $val['label'],
	            'content'   => $this->getLayout()
                                  ->createBlock('adminhtml/catalog_product_edit_tab_alerts_customers',$key,array('id'=>$key))
                                  ->setId($key)
                                  ->setData('customerIds',$customerIds)
                                  ->setData('productId',$product_id)
                                  ->setData('store',$storeId),
                                  
	            'open'      => false,
            ));
        }
        $this->setChild('accordion', $accordion);
        
        return parent::_prepareLayout();
    }
    
    public function getAccordionHtml()
    {
        return $this->getChildHtml('accordion');
    }
}