<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Controller\Adminhtml\Rma;

class Psl extends \Magento\Rma\Controller\Adminhtml\Rma
{
    /**
     * Configuration for popup window for packaging
     *
     * @param \Magento\Rma\Model\Rma $model
     * @return string
     */
    protected function _getConfigDataJson($model)
    {
        $urlParams = [];
        $itemsQty = [];
        $itemsPrice = [];
        $itemsName = [];
        $itemsWeight = [];
        $itemsProductId = [];

        $urlParams['id'] = $model->getId();
        $items = $model->getShippingMethods(true);

        $createLabelUrl = $this->getUrl('adminhtml/*/saveShipping', $urlParams);
        $itemsGridUrl = $this->getUrl('adminhtml/*/getShippingItemsGrid', $urlParams);
        $thisPage = $this->getUrl('adminhtml/*/edit', $urlParams);

        $code = $this->getRequest()->getParam('method');
        $carrier = $this->_objectManager->get('Magento\Rma\Helper\Data')->getCarrier($code, $model->getStoreId());
        if ($carrier) {
            $getCustomizableContainers = $carrier->getCustomizableContainerTypes();
        }

        foreach ($items as $item) {
            $itemsQty[$item->getItemId()] = $item->getQty();
            $itemsPrice[$item->getItemId()] = $item->getPrice();
            $itemsName[$item->getItemId()] = $item->getName();
            $itemsWeight[$item->getItemId()] = $item->getWeight();
            $itemsProductId[$item->getItemId()] = $item->getProductId();
            $itemsOrderItemId[$item->getItemId()] = $item->getItemId();
        }

        $shippingInformation = $this->_view->getLayout()->createBlock(
            'Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\Shipping\Information'
        )->setIndex(
            $this->getRequest()->getParam('index')
        )->toHtml();

        $data = [
            'createLabelUrl' => $createLabelUrl,
            'itemsGridUrl' => $itemsGridUrl,
            'errorQtyOverLimit' => __(
                "A quantity you're trying to add is higher than the number of products we shipped."
            ),
            'titleDisabledSaveBtn' => __('Products should be added to package(s)'),
            'validationErrorMsg' => __('You entered an invalid value.'),
            'shipmentItemsQty' => $itemsQty,
            'shipmentItemsPrice' => $itemsPrice,
            'shipmentItemsName' => $itemsName,
            'shipmentItemsWeight' => $itemsWeight,
            'shipmentItemsProductId' => $itemsProductId,
            'shipmentItemsOrderItemId' => $itemsOrderItemId,
            'shippingInformation' => $shippingInformation,
            'thisPage' => $thisPage,
            'customizable' => $getCustomizableContainers,
        ];

        return $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($data);
    }

    /**
     * Shows available shipping methods
     *
     * @return void|\Zend_Controller_Response_Abstract
     * @throws \Magento\Framework\Model\Exception
     */
    public function execute()
    {
        $data = $this->getRequest()->getParam('data');
        $response = false;

        try {
            $model = $this->_initModel();
            if (!$model->getId()) {
                throw new \Magento\Framework\Model\Exception(__('This is the wrong RMA ID.'));
            }
        } catch (\Magento\Framework\Model\Exception $e) {
            $response = ['error' => true, 'message' => $e->getMessage()];
        } catch (\Exception $e) {
            $response = ['error' => true, 'message' => __('We cannot display the available shipping methods.')];
        }

        if ($data) {
            return $this->getResponse()->representJson($this->_getConfigDataJson($model));
        }

        $this->_view->loadLayout();
        $response = $this->_view->getLayout()->getBlock('magento_rma_shipment_packaging')->toHtml();

        if (is_array($response)) {
            $this->getResponse()->representJson(
                $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($response)
            );
        } else {
            $this->getResponse()->setBody($response);
        }
    }
}
