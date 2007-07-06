<?php
/**
 * Customer admin controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_CustomerController extends Mage_Core_Controller_Front_Action 
{
    /**
     * Customers list action
     */
    public function indexAction()
    {
        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }
        $this->loadLayout('baseframe');
        
        /**
         * Set active menu item
         */
        $this->getLayout()->getBlock('menu')->setActive('customer');
        
        /**
         * Append customers block to content
         */
        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('adminhtml/customers', 'customers')
        );
        
        /**
         * Add breadcrumb item
         */
        $this->getLayout()->getBlock('breadcrumbs')
            ->addLink(__('customers'), __('customers title'));

        $this->renderLayout();
    }
    
    public function gridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/customer_grid')->toHtml());
    }
    
    /**
     * Customer edit action
     */
    public function editAction()
    {
        $this->loadLayout('baseframe');
        
        $customerId = (int) $this->getRequest()->getParam('id');
        $customer = Mage::getModel('customer/customer');
        
        if ($customerId) {
            $customer->load($customerId);
        }
        
        Mage::register('customer', $customer);

        /**
         * Set active menu item
         */
        $this->getLayout()->getBlock('menu')->setActive('customer/new');
        
        /**
         * Append customer edit block to content
         */
        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('adminhtml/customer_edit')
        );
        
        /**
         * Add breadcrunb items
         */
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')
            ->addLink(__('customers'), __('customers title'), Mage::getUrl('adminhtml/customer'));
        
        if ($customerId) {
            $breadcrumbs->addLink(__('customer').' #'.$customerId, __('customer').' #'.$customerId);
        }
        else {
            $breadcrumbs->addLink(__('new customer'), __('new customer title'));
        }
        
        /**
         * Append customer edit tabs to left block
         */
        $this->getLayout()->getBlock('left')->append($this->getLayout()->createBlock('adminhtml/customer_tabs'));
        
        $this->renderLayout();
    }
    
    /**
     * Create new customer action
     */
    public function newAction()
    {
        $this->_forward('edit');
    }
    
    /**
     * Delete customer action
     */
    public function deleteAction()
    {
        $customerId = (int) $this->getRequest()->getParam('id');
        if ($customerId) {
            try {
                $customer = Mage::getModel('customer/customer')
                    ->setId($customerId)
                    ->delete();
                //Mage::getSingleton('adminhtml/session')->addMessage();
            }
            catch (Exception $e){
                //Mage::getSingleton('adminhtml/session')->addMessage();
            }
        }
        $this->getResponse()->setRedirect(Mage::getUrl('adminhtml/customer'));
    }
    
    /**
     * Save customer action
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            
            $customer = Mage::getModel('customer/customer')
                ->addData($data);
            
            if ($customerId = (int) $this->getRequest()->getParam('id')) {
                $customer->setId($customerId);
            }
            
            if (isset($data['address'])) {
                // unset template data
                if (isset($data['address']['_template_'])) {
                    unset($data['address']['_template_']);
                }
                
                foreach ($data['address'] as $index => $addressData) {
                    $address = Mage::getModel('customer/address');
                    $address->setData($addressData);
                    
                    if ($addressId = (int) $index) {
                        $address->setId($addressId);
                    }
                    /**
                     * We need set post_index for detect default addresses
                     */
                    $address->setPostIndex($index);
                	$customer->addAddress($address);
                }
            }

            try {
                $customer->save();
            }
            catch (Exception $e){
                echo $e;
            }
        }
        
        $this->getResponse()->setRedirect(Mage::getUrl('adminhtml/customer'));
    }
    
    /**
     * Export customer grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName   = 'customers.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/customer_grid')
            ->getCsv();
        
        $this->_sendUploadResponce($fileName, $content);
    }
    
    /**
     * Export customer grid to XML format
     */
    public function exportXmlAction()
    {
        $fileName   = 'customers.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/customer_grid')
            ->getXml();
        
        $this->_sendUploadResponce($fileName, $content);
    }
    
    protected function _sendUploadResponce($fileName, $content)
    {
        header('HTTP/1.1 200 OK');
        header('Content-Disposition: attachment; filename='.$fileName);
        header('Last-Modified: '.date('r'));
        header("Accept-Ranges: bytes");
        header("Content-Length: ".sizeof($content));
        header("Content-type: application/octet-stream");
        echo $content;        
    }
}
