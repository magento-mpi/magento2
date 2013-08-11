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
        $this->_title(__('Newsletter Subscribers'));

        if ($this->getRequest()->getParam('ajax')) {
            $this->_forward('grid');
            return;
        }

        $this->loadLayout();

        $this->_setActiveMenu('Mage_Newsletter::newsletter_subscriber');

        $this->_addBreadcrumb(__('Newsletter'), __('Newsletter'));
        $this->_addBreadcrumb(__('Subscribers'), __('Subscribers'));

        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
     }

    /**
     * Export subscribers grid to CSV format
     */
    public function exportCsvAction()
    {
        $this->loadLayout();
        $fileName = 'subscribers.csv';
        $content = $this->getLayout()->getChildBlock('adminhtml.newslettrer.subscriber.grid', 'grid.export');

        $this->_prepareDownloadResponse($fileName, $content->getCsvFile($fileName));
    }

    /**
     * Export subscribers grid to XML format
     */
    public function exportXmlAction()
    {
        $this->loadLayout();
        $fileName = 'subscribers.xml';
        $content = $this->getLayout()->getChildBlock('adminhtml.newslettrer.subscriber.grid', 'grid.export');
        $this->_prepareDownloadResponse($fileName, $content->getExcelFile($fileName));
    }

    public function massUnsubscribeAction()
    {
        $subscribersIds = $this->getRequest()->getParam('subscriber');
        if (!is_array($subscribersIds)) {
             Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(__('Please select one or more subscribers.'));
        }
        else {
            try {
                foreach ($subscribersIds as $subscriberId) {
                    $subscriber = Mage::getModel('Mage_Newsletter_Model_Subscriber')->load($subscriberId);
                    $subscriber->unsubscribe();
                }
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addSuccess(
                    __('A total of %1 record(s) were updated.', count($subscribersIds))
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
             Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(__('Please select one or more subscribers.'));
        }
        else {
            try {
                foreach ($subscribersIds as $subscriberId) {
                    $subscriber = Mage::getModel('Mage_Newsletter_Model_Subscriber')->load($subscriberId);
                    $subscriber->delete();
                }
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addSuccess(
                    __('Total of %1 record(s) were deleted', count($subscribersIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mage_Newsletter::subscriber');
    }
}
