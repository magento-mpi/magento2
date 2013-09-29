<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Shipping\Model\Rate\Result;

class Error extends \Magento\Shipping\Model\Rate\Result\AbstractResult
{

    public function getErrorMessage()
    {
        if (!$this->getData('error_message')) {
            $this->setData('error_message', __('This shipping method is not available. To use this shipping method, please contact us.'));
        }
        return $this->getData('error_message');
    }
}
