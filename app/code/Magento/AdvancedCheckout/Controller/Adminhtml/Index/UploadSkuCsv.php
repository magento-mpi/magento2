<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdvancedCheckout\Controller\Adminhtml\Index;

use Magento\Framework\Model\Exception;

class UploadSkuCsv extends \Magento\AdvancedCheckout\Controller\Adminhtml\Index
{
    /**
     * Upload and parse CSV file with SKUs and quantity
     *
     * @return void
     */
    public function execute()
    {
        try {
            $this->_initData();
        } catch (Exception $e) {
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
            $this->_redirect('customer/index');
            $this->_redirectFlag = true;
        }
        if ($this->_redirectFlag) {
            return;
        }

        /** @var $helper \Magento\AdvancedCheckout\Helper\Data */
        $helper = $this->_objectManager->get('Magento\AdvancedCheckout\Helper\Data');
        $rows = $helper->isSkuFileUploaded($this->getRequest()) ? $helper->processSkuFileUploading() : array();

        $items = $this->getRequest()->getPost('add_by_sku');
        if (!is_array($items)) {
            $items = array();
        }
        $result = array();
        foreach ($items as $sku => $qty) {
            $result[] = array('sku' => $sku, 'qty' => $qty['qty']);
        }
        foreach ($rows as $row) {
            $result[] = $row;
        }

        if (!empty($result)) {
            $cart = $this->getCartModel();
            $cart->prepareAddProductsBySku($result);
            $cart->saveAffectedProducts($this->getCartModel(), true);
        }

        $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl($this->getUrl('*')));
    }
}
