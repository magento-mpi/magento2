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
        $responseContent = '';
        $rateId = (int)$this->getRequest()->getParam('tax_calculation_rate_id');
        try {
            $rate = $this->_objectManager->create('Magento\Tax\Model\Calculation\Rate')->load($rateId);
            $rate->delete();
            $responseContent = $this->_objectManager->get(
                'Magento\Core\Helper\Data'
            )->jsonEncode(
                array('success' => true, 'error_message' => '')
            );
        } catch (\Magento\Framework\Model\Exception $e) {
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
