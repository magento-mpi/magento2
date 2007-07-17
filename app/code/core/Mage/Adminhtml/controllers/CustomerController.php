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
class Mage_Adminhtml_CustomerController extends Mage_Adminhtml_Controller_Action
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
        $this->_initLayoutMessages('adminhtml/session');

        /**
         * Set active menu item
         */
        $this->_setActiveMenu('customer/manage');

        /**
         * Append customers block to content
         */
        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/customers', 'customers')
        );

        /**
         * Add breadcrumb item
         */
        $this->_addBreadcrumb(__('Customers'), __('customers title'));

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
        $this->_initLayoutMessages('adminhtml/session');
        
        $customerId = (int) $this->getRequest()->getParam('id');
        $customer = Mage::getModel('customer/customer');
        
        if ($customerId) {
            $customer->load($customerId);
        }
        
        if ($data = Mage::getSingleton('adminhtml/session')->getCustomerData(true)) {
            $customer->addData($data);
        }

        Mage::register('customer', $customer);

        /**
         * Set active menu item
         */
        $this->_setActiveMenu('customer/new');

        /**
         * Append customer edit block to content
         */
        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/customer_edit')
        );

        /**
         * Add breadcrunb items
         */
        $this->_addBreadcrumb(__('Customers'), __('customers title'), Mage::getUrl('adminhtml/customer'));

        if ($customerId) {
            $this->_addBreadcrumb(__('Customer').' #'.$customerId, __('customer').' #'.$customerId);
        }
        else {
            $this->_addBreadcrumb(__('New Customer'), __('new customer title'));
        }

        /**
         * Append customer edit tabs to left block
         */
        $this->getLayout()->getBlock('left')->append($this->getLayout()->createBlock('adminhtml/customer_edit_tabs'));

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
                Mage::getSingleton('adminhtml/session')->addSuccess('Customer was deleted');
            }
            catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('adminhtml/customer');
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

            if(isset($data['subscription'])) {
				$customer->setIsSubscribed(true);
            } else {
				$customer->setIsSubscribed(false);
			}

            try {
                $customer->save();
                Mage::getSingleton('adminhtml/session')->addSuccess('Customer was saved');
            }
            catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setCustomerData($data);
                $this->getResponse()->setRedirect(Mage::getUrl('*/customer/edit'));
                return;
            }
        }
        $this->getResponse()->setRedirect(Mage::getUrl('*/customer'));
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

    /**
     * Customer orders grid
     *
     */
    public function ordersAction() {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/customer_edit_tab_orders')->toHtml());
    }
}
