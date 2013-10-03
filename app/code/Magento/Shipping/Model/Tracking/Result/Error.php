<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Shipping\Model\Tracking\Result;

class Error extends \Magento\Shipping\Model\Tracking\Result\AbstractResult
{
    public function getAllData()
    {
        return $this->_data;
    }

    public function getErrorMessage()
    {
        return  __('Tracking information is unavailable.');
    }
}
