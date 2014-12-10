<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
        $rows = $helper->isSkuFileUploaded($this->getRequest()) ? $helper->processSkuFileUploading() : [];

        $items = $this->getRequest()->getPost('items');
        if (!is_array($items)) {
            $items = [];
        }
        foreach ($rows as $row) {
            $items[] = $row;
        }

        $this->getRequest()->setParam('items', $items);
        $this->_forward('advancedAdd', 'cart');
    }
}
