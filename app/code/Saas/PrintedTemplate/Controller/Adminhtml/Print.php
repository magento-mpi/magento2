<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Print controller for printing invoice, creditmemo and shipment using templates
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Controllers
 */
class Saas_PrintedTemplate_Controller_Adminhtml_Print extends Magento_Adminhtml_Controller_Action
{
    /**
     * Print PDF for entity: invoice, creditmemo or shipment
     */
    public function entityAction()
    {
        $type = $this->getRequest()->getParam('type');
        $orderTypeId = $this->getRequest()->getParam('id');

        if (!$orderTypeId || !$type) {
            $this->_forward('noRoute');
            return;
        }

        try {
            $entity = Mage::getModel(uc_words("Mage_Sales_Model_Order_$type"));
            if (!$entity) {
                Mage::throwException($this->__('Cannot load %s entity; please reload page and try again.', $type));
            }

            $entity->load($orderTypeId);
            if (!$entity->getId()) {
                Mage::throwException(
                    $this->__('Cannot load %s entity #%s; please reload page and try again.', $type, $orderTypeId)
                );
            }

            $pdf = Mage::helper('Saas_PrintedTemplate_Helper_Locator')->getConverter($entity)->getPdf();
            $fileName = $type . Mage::getSingleton('Mage_Core_Model_Date')->date('Y-m-d_H-i-s') . '.pdf';

            $this->_prepareDownloadResponse($fileName, $pdf, 'application/pdf');
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirectReferer();
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($this->__('Cannot generate PDF.'));
            $this->_redirectReferer();
        }
    }

    /**
     * Mass PDF print for entutis: invoice, creditmemo or shipment
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function entitiesAction()
    {
        $type = $this->getRequest()->getParam('type');
        $ids = $this->getRequest()->getPost($type . '_ids');
        $orderIds = $this->getRequest()->getPost('order_ids');

        if (
            !($ids || $orderIds) || !$type
            || !in_array($type, array('invoice', 'creditmemo', 'shipment'))
        ) {
            $this->_getSession()->addError($this->__('Please select entities to print.'));
            $this->_redirectReferer();
            return;
        }

        $entity = Mage::getModel(uc_words("Mage_Sales_Model_Order_$type"));
        if (!$entity) {
            Mage::throwException($this->__('Cannot load %s entity; please reload page and try again.', $type));
        }
        $collection = $entity->getCollection();
        if (!empty($orderIds)) {
            $collection->addAttributeToFilter('order_id', array('in' => $orderIds));
        } else {
            $collection->addAttributeToFilter('entity_id', array('in' => $ids));
        }

        try {
            if (count($collection) == 0) {
                Mage::throwException($this->__('There are no printable documents related to selected orders.'));
            }

            $pdf = Mage::getModel('Saas_PrintedTemplate_Model_Converter_Batch', array('collection' => $collection))
                ->getPdf();
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirectReferer();
            return;
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($this->__('Cannot generate PDF.'));
            $this->_redirectReferer();
            return;
        }

        $fileName = $type . Mage::getSingleton('Mage_Core_Model_Date')->date('Y-m-d_H-i-s') . '.pdf';
        $this->_prepareDownloadResponse($fileName, $pdf, 'application/pdf');
    }

    /**
     * Mass PDF print all invoices, creditmemos and shipments for selected orders
     */
    public function allEntitiesAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids');
        if (!$orderIds) {
            $this->_getSession()->addError($this->__('Please select orders to print.'));
            $this->_redirectReferer();
            return;
        }

        try {
            $collection = $this->_getPrintEntitiesCollection($orderIds);
            if (count($collection) == 0) {
                Mage::throwException($this->__('There are no printable documents related to selected orders.'));
            }

            $pdf = Mage::getModel('Saas_PrintedTemplate_Model_Converter_Batch', array('collection' => $collection))
                ->getPdf();
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirectReferer();
            return;
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($this->__('Cannot generate PDF.'));
            $this->_redirectReferer();
            return;
        }

        $fileName = 'docs' . Mage::getSingleton('Mage_Core_Model_Date')->date('Y-m-d_H-i-s') . '.pdf';
        $this->_prepareDownloadResponse($fileName, $pdf, 'application/pdf');
    }

    /**
     * Prepare array of objects wich should be printed for specified orders
     *
     * @param array $orderIds
     * @return array
     */
    protected function _getPrintEntitiesCollection(array $orderIds)
    {
        $collection = array();
        foreach ($orderIds as $orderId) {
            $invoices = Mage::getResourceModel('Mage_Sales_Model_Resource_Order_Invoice_Collection')
                ->setOrderFilter($orderId)
                ->getItems();
            $creditmemos = Mage::getResourceModel('Mage_Sales_Model_Resource_Order_Creditmemo_Collection')
                ->setOrderFilter($orderId)
                ->getItems();
            $shipments = Mage::getResourceModel('Mage_Sales_Model_Resource_Order_Shipment_Collection')
                ->setOrderFilter($orderId)
                ->getItems();

            $collection = array_merge($collection, $invoices, $creditmemos, $shipments);
        }

        return $collection;
    }

    /**
     * Check if allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization
            ->isAllowed('Saas_PrintedTemplate::print');
    }
}
