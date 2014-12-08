<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml\Order\Create;


class ConfigureProductToAdd extends \Magento\Sales\Controller\Adminhtml\Order\Create
{
    /**
     * Ajax handler to response configuration fieldset of composite product in order
     *
     * @return void
     */
    public function execute()
    {
        // Prepare data
        $productId = (int)$this->getRequest()->getParam('id');

        $configureResult = new \Magento\Framework\Object();
        $configureResult->setOk(true);
        $configureResult->setProductId($productId);
        $sessionQuote = $this->_objectManager->get('Magento\Backend\Model\Session\Quote');
        $configureResult->setCurrentStoreId($sessionQuote->getStore()->getId());
        $configureResult->setCurrentCustomerId($sessionQuote->getCustomerId());

        // Render page
        $this->_objectManager->get(
            'Magento\Catalog\Helper\Product\Composite'
        )->renderConfigureResult(
            $configureResult
        );
    }
}
