<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Rma_Block_Return_Tracking_Popup extends Magento_Shipping_Block_Tracking_Popup
{
    /**
     * Retrieve array of tracking info
     *
     * @return array
     */
    public function getTrackingInfo()
    {
        /* @var $info Magento_Rma_Model_Shipping_Info */
        $info = $this->_coreRegistry->registry('rma_current_shipping');

        return $info->getTrackingInfo();
    }

}
