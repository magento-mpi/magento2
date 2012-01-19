<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Enterprise checkout index controller
 *
 * @category   Enterprise
 * @package    Enterprise_Checkout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Checkout_SkuController extends Mage_Core_Controller_Front_Action
{

    /**
     * Check functionality is enabled and applicable to the Customer
     *
     * @return Enterprise_Checkout_IndexController
     */
    public function preDispatch()
    {
        parent::preDispatch();

        // guest redirected to "Login or Create an Account" page
        /** @var $customerSession Mage_Customer_Model_Session */
        $customerSession = Mage::getSingleton('Mage_Customer_Model_Session');
        if (!$customerSession->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
            return $this;
        }

        /** @var $helper Enterprise_Checkout_Helper_Data */
        $helper = Mage::helper('Enterprise_Checkout_Helper_Data');
        if (!$helper->isSkuEnabled() || !$helper->isSkuApplied()) {
            $this->_redirect('customer/account');
        }

        return $this;
    }

    /**
     * View Order by SKU page in 'My Account' section
     *
     * @return void
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('Mage_Customer_Model_Session');
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(Mage::helper('Enterprise_Checkout_Helper_Data')->__('Order by SKU'));
        }
        $this->renderLayout();
    }

    /**
     * Upload file Action
     *
     * @return void
     */
    public function uploadFileAction()
    {
        $data = $this->getRequest()->getPost();
        $rows = array();
        $uploadError = false;
        if ($data) {
            /** @var $importModel Enterprise_Checkout_Model_Import */
            $importModel = Mage::getModel('Enterprise_Checkout_Model_Import');

            try {
                if ($importModel->uploadFile()) {
                    $rows = $importModel->getRows();
                }
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addException($e, $e->getMessage());
                $uploadError = true;
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('Enterprise_Checkout_Helper_Data')->__('File upload error.')
                );
                $uploadError = true;
            }

            if (!empty($data['items'])) {
                foreach ($data['items'] as $item) {
                    if (!empty($item['sku']) && !empty($item['qty'])) {
                        $rows[] = $item;
                    }
                }
            }
            if (empty($rows) && !$uploadError) {
                $this->_getSession()->addError(Mage::helper('Enterprise_Checkout_Helper_Data')->__('File is empty.'));
            } else {
                $this->getRequest()->setParam('items', $rows);
                $this->_forward('advancedAdd', 'cart');
                return;
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Get checkout session model instance
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('Mage_Checkout_Model_Session');
    }
}
