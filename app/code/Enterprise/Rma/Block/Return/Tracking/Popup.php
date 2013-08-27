<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Rma_Block_Return_Tracking_Popup extends Magento_Shipping_Block_Tracking_Popup
{
    /**
     * Retrieve array of tracking info
     *
     * @return array
     */
    public function getTrackingInfo()
    {
        /* @var $info Enterprise_Rma_Model_Shipping_Info */
        $info = Mage::registry('rma_current_shipping');

        return $info->getTrackingInfo();
    }

}
