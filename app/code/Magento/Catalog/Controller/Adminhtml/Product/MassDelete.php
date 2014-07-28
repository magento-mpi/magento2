<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Product;

class MassDelete extends \Magento\Catalog\Controller\Adminhtml\Product
{
    /**
     * @return void
     */
    public function execute()
    {
        $productIds = $this->getRequest()->getParam('product');
        if (!is_array($productIds) || empty($productIds)) {
            $this->messageManager->addError(__('Please select product(s).'));
        } else {
            try {
                foreach ($productIds as $productId) {
                    $product = $this->_objectManager->get('Magento\Catalog\Model\Product')->load($productId);
                    $product->delete();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been deleted.', count($productIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $this->_redirect('catalog/*/index');
    }
}
