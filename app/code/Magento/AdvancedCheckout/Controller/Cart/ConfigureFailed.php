<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdvancedCheckout\Controller\Cart;

class ConfigureFailed extends \Magento\AdvancedCheckout\Controller\Cart
{
    /**
     * Configure failed item options
     *
     * @return void
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $qty = $this->getRequest()->getParam('qty', 1);

        try {
            $params = new \Magento\Framework\Object();
            $params->setCategoryId(false);
            $params->setConfigureMode(true);

            $buyRequest = new \Magento\Framework\Object(array('product' => $id, 'qty' => $qty));

            $params->setBuyRequest($buyRequest);

            /** @var \Magento\Catalog\Helper\Product\View $view */
            $view = $this->_objectManager->get('Magento\Catalog\Helper\Product\View');
            $params->setBeforeHandles(array('catalog_product_view'));
            $view->prepareAndRender($id, $this, $params);
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('*');
            return;
        } catch (\Exception $e) {
            $this->messageManager->addError(__('You cannot configure a product.'));
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
            $this->_redirect('*');
            return;
        }
    }
}
