<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdvancedCheckout\Controller\Sku;

class UploadFile extends \Magento\AdvancedCheckout\Controller\Sku
{
    /**
     * Upload file Action
     *
     * @return void
     */
    public function execute()
    {
        /** @var $helper \Magento\AdvancedCheckout\Helper\Data */
        $helper = $this->_objectManager->get('Magento\AdvancedCheckout\Helper\Data');
        $rows = $helper->isSkuFileUploaded($this->getRequest()) ? $helper->processSkuFileUploading() : array();

        $items = $this->getRequest()->getPost('items');
        if (!is_array($items)) {
            $items = array();
        }
        foreach ($rows as $row) {
            $items[] = $row;
        }

        $this->getRequest()->setParam('items', $items);
        $this->_forward('advancedAdd', 'cart');
    }
}
