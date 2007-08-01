<?php
/**
 * Adminhtml tax rate controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Tax_RateController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_initAction()
            ->_addBreadcrumb(__('Tax Rates'), __('Tax Rates Title'))
            ->_addContent(
                $this->getLayout()->createBlock('adminhtml/tax_rate_toolbar_add', 'tax_rate_toolbar')
                    ->assign('createUrl', Mage::getUrl('adminhtml/tax_rate/add'))
                    ->assign('header', __('Tax Rates'))
            )
            ->_addContent($this->getLayout()->createBlock('adminhtml/tax_rate_grid', 'tax_rate_grid'))
            ->renderLayout();
    }

    public function addAction()
    {
        $this->_initAction()
            ->_addBreadcrumb(__('Tax Rates'), __('Tax Rates Title'), Mage::getUrl('adminhtml/tax_rate'))
            ->_addBreadcrumb(__('New Tax Rate'), __('New Tax Rate Title'))
            ->_addContent(
                $this->getLayout()->createBlock('adminhtml/tax_rate_toolbar_save')
                ->assign('header', __('Add New Tax Rate'))
                ->assign('form', $this->getLayout()->createBlock('adminhtml/tax_rate_form_add'))
            )
            ->renderLayout();
    }

    public function saveAction()
    {
        if( $postData = $this->getRequest()->getPost() ) {
            try {
                $rateModel = Mage::getSingleton('tax/rate');
                $rateModel->setData($postData);
                $rateModel->save();
                $this->getResponse()->setRedirect(Mage::getUrl("*/*/"));
                Mage::getSingleton('adminhtml/session')->addSuccess('Tax rate successfully saved.');
                $this->getResponse()->setRedirect(Mage::getUrl('*/*/'));
            } catch (Exception $e) {
                if ($referer = $this->getRequest()->getServer('HTTP_REFERER')) {
                    $this->getResponse()->setRedirect($referer);
                }
                Mage::getSingleton('adminhtml/session')->addError('Error while saving this rate. Please try again later.');
            }
        }
    }

    public function editAction()
    {
        $this->_initAction()
            ->_addBreadcrumb(__('Tax Rates'), __('Tax Rates Title'), Mage::getUrl('adminhtml/tax_rate'))
            ->_addBreadcrumb(__('Edit Tax Rate'), __('Edit Tax Rate Title'))
            ->_addContent(
                $this->getLayout()->createBlock('adminhtml/tax_rate_toolbar_save')
                ->assign('header', __('Edit Tax Rate'))
                ->assign('form', $this->getLayout()->createBlock('adminhtml/tax_rate_form_add'))
            )
            ->renderLayout();
    }

    public function deleteAction()
    {
        if( $rateId = $this->getRequest()->getParam('rate') ) {
            try {
                $rateModel = Mage::getSingleton('tax/rate');
                $rateModel->setRateId($rateId);
                $rateModel->delete();
                $this->getResponse()->setRedirect(Mage::getUrl("*/*/"));
                Mage::getSingleton('adminhtml/session')->addSuccess('Tax rate successfully deleted.');
                $this->getResponse()->setRedirect(Mage::getUrl('*/*/'));
            } catch (Exception $e) {
                if ($referer = $this->getRequest()->getServer('HTTP_REFERER')) {
                    $this->getResponse()->setRedirect($referer);
                }
                Mage::getSingleton('adminhtml/session')->addError('Error while deleting this rate. Please try again later.');
            }
        }
    }

    /**
     * Export rates grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName   = 'rates.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/tax_rate_grid')
            ->getCsv();

        $this->_sendUploadResponce($fileName, $content);
    }

    /**
     * Export rates grid to XML format
     */
    public function exportXmlAction()
    {
        $fileName   = 'rates.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/tax_rate_grid')
            ->getXml();

        $this->_sendUploadResponce($fileName, $content);
    }

    /**
     * Initialize action
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _initAction()
    {
        $this->loadLayout('baseframe')
            ->_setActiveMenu('sales/tax/tax_rates')
            ->_addBreadcrumb(__('Sales'), __('Sales Title'))
            ->_addBreadcrumb(__('Tax'), __('Tax Title'));
        return $this;
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