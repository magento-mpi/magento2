<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Block\Return\Tracking;

class Popup extends \Magento\Shipping\Block\Tracking\Popup
{
    /**
     * Retrieve array of tracking info
     *
     * @return array
     */
    public function getTrackingInfo()
    {
        /* @var $info \Magento\Rma\Model\Shipping\Info */
        $info = \Mage::registry('rma_current_shipping');

        return $info->getTrackingInfo();
    }

}
