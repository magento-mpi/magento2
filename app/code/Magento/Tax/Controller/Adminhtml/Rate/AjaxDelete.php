<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Controller\Adminhtml\Rate;

class AjaxDelete extends \Magento\Tax\Controller\Adminhtml\Rate
{
    /**
     * Delete Tax Rate via AJAX
     *
     * @return void
     */
    public function execute()
    {
        $rateId = (int)$this->getRequest()->getParam('tax_calculation_rate_id');
        try {
            $this->_taxRateService->deleteTaxRate($rateId);
            $responseContent = $this->_objectManager->get(
                'Magento\Core\Helper\Data'
            )->jsonEncode(
                array('success' => true, 'error_message' => '')
            );
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $responseContent = $this->_objectManager->get(
                'Magento\Core\Helper\Data'
            )->jsonEncode(
                array('success' => false, 'error_message' => $e->getMessage())
            );
        } catch (\Exception $e) {
            $responseContent = $this->_objectManager->get(
                'Magento\Core\Helper\Data'
            )->jsonEncode(
                array('success' => false, 'error_message' => __('An error occurred while deleting this tax rate.'))
            );
        }
        $this->getResponse()->representJson($responseContent);
    }
}
