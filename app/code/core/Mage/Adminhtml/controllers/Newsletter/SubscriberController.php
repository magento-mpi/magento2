<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml newsletter subscribers controller
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Newsletter_SubscriberController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
        $this->_title($this->__('Newsletter'))->_title($this->__('Newsletter Subscribers'));

        if ($this->getRequest()->getParam('ajax')) {
            $this->_forward('grid');
            return;
        }

        $this->loadLayout();

        $this->_setActiveMenu('Mage_Newsletter::newsletter_subscriber');

        $this->_addBreadcrumb(Mage::helper('Mage_Newsletter_Helper_Data')->__('Newsletter'), Mage::helper('Mage_Newsletter_Helper_Data')->__('Newsletter'));
        $this->_addBreadcrumb(Mage::helper('Mage_Newsletter_Helper_Data')->__('Subscribers'), Mage::helper('Mage_Newsletter_Helper_Data')->__('Subscribers'));

        $this->_addContent(
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Newsletter_Subscriber','subscriber')
        );

        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Newsletter_Subscriber_Grid')->toHtml()
        );
    }

    /**
     * Export subscribers grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName   = 'subscribers.csv';
        $content    = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Newsletter_Subscriber_Grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export subscribers grid to XML format
     */
    public function exportXmlAction()
    {
        $fileName   = 'subscribers.xml';
        $content    = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Newsletter_Subscriber_Grid')
            ->getExcelFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function massUnsubscribeAction()
    {
        $subscribersIds = $this->getRequest()->getParam('subscriber');
        if (!is_array($subscribersIds)) {
             Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(Mage::helper('Mage_Newsletter_Helper_Data')->__('Please select subscriber(s)'));
        }
        else {
            try {
                foreach ($subscribersIds as $subscriberId) {
                    $subscriber = Mage::getModel('Mage_Newsletter_Model_Subscriber')->load($subscriberId);
                    $subscriber->unsubscribe();
                }
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addSuccess(
                    Mage::helper('Mage_Adminhtml_Helper_Data')->__('Total of %d record(s) were updated', count($subscribersIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    public function massDeleteAction()
    {
        $subscribersIds = $this->getRequest()->getParam('subscriber');
        if (!is_array($subscribersIds)) {
             Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(Mage::helper('Mage_Newsletter_Helper_Data')->__('Please select subscriber(s)'));
        }
        else {
            try {
                foreach ($subscribersIds as $subscriberId) {
                    $subscriber = Mage::getModel('Mage_Newsletter_Model_Subscriber')->load($subscriberId);
                    $subscriber->delete();
                }
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addSuccess(
                    Mage::helper('Mage_Adminhtml_Helper_Data')->__('Total of %d record(s) were deleted', count($subscribersIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('newsletter/subscriber');
    }
}
