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
        $product_id = isset($params['id'])?$params['id']:0;
        $storeId = isset($params['store'])?$params['store']:0;

        if($storeId){
            $accordion = $this->getLayout()->createBlock('adminhtml/widget_accordion')
                ->setId('AlertsBlockId');
            $messages = array();
            foreach ($this->getAlerts() as $key=>$val) {
                $typeModel = Mage::getModel(Mage::getConfig()->getNode('global/customeralert/types/'.$key.'/model'));
                $customerIds = $typeModel 
                             ->setProductId($product_id)
                             ->setStoreId($storeId)
                             ->check()
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
                if($typeModel->getCheckedText())
                    $messages[] = array('method'=>'notice','label'=>$typeModel->getCheckedText());
            }
            
            $button = $this->getLayout()->createBlock('adminhtml/widget_button');
            $this->setChild('accordion', $accordion);
            $this->setChild('addToQuery_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label'     => __('Add Customers To Query'),
                        //'onclick'   => "tierPriceControl.deleteItem('#{index}')",
                        'class' => 'add'
                    )));
        } else {
            $messages[] = array('method'=>'error','label'=>__('No one store was selected.'));
        }
        
        $message = $this->getLayout()->createBlock('core/messages');
        foreach ($messages as $mess) {
            $message->getMessageCollection()->add(Mage::getSingleton('core/message')->$mess['method']($mess['label']));
        }
        $this->setChild('message', $message);
            
            
        
        return parent::_prepareLayout();
    }
    
    public function getAddToQueryButtonHtml()
    {
        return $this->getChildHtml('addToQuery_button');
    }
    
    public function getAccordionHtml()
    {
        return $this->getChildHtml('accordion');
    }
    
    public function getMessageHtml()
    {
        return $this->getChildHtml('message');
    }
    
}