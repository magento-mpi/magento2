<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Controller\Adminhtml\Rate;

class AjaxSave extends \Magento\Tax\Controller\Adminhtml\Rate
{
    /**
     * Save Tax Rate via AJAX
     *
     * @return void
     */
    public function execute()
    {
        $responseContent = '';
        try {
            $rateData = $this->_processRateData($this->getRequest()->getPost());
            $taxRate = $this->populateTaxRateData($rateData);
            $taxRateId = $taxRate->getId();
            if ($taxRateId) {
                $this->_taxRateService->updateTaxRate($taxRate);
            } else {
                $taxRate = $this->_taxRateService->createTaxRate($taxRate);
                $taxRateId = $taxRate->getId();
            }
            $responseContent = $this->_objectManager->get(
                'Magento\Core\Helper\Data'
            )->jsonEncode(
                array(
                    'success' => true,
                    'error_message' => '',
                    'tax_calculation_rate_id' => $taxRate->getId(),
                    'code' => $taxRate->getCode()
                )
            );
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $responseContent = $this->_objectManager->get(
                'Magento\Core\Helper\Data'
            )->jsonEncode(
                array(
                    'success' => false,
                    'error_message' => $e->getMessage(),
                    'tax_calculation_rate_id' => '',
                    'code' => ''
                )
            );
        } catch (\Exception $e) {
            $responseContent = $this->_objectManager->get(
                'Magento\Core\Helper\Data'
            )->jsonEncode(
                array(
                    'success' => false,
                    'error_message' => __('Something went wrong saving this rate.'),
                    'tax_calculation_rate_id' => '',
                    'code' => ''
                )
            );
        }
        $this->getResponse()->representJson($responseContent);
    }
}
