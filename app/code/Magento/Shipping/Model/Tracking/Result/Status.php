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

/**
 * Fields:
 * - carrier: carrier_code
 * - carrierTitle: Carrier Title
 * - tracking: 749011111111
 * - status: delivered
 * - service: home delivery
 * - delivery date: 2007-11-23
 * - delivery time: 16:01:00
 * - delivery location: Frontdoor
 * - signedby: lindy
 *
 * Fields:
 * -carrier: ups cgi
 * -popup: 1
 * -url: http://wwwapps.ups.com/WebTracking/processInputRequest?HTMLVersion=5.0&error_carried=true&tracknums_displayed=5&TypeOfInquiryNumber=T&loc=en_US&InquiryNumber1=$tracking
 *
 * Fields:
 * -carrier: usps
 * -tracksummary: Your item was delivered at 6:50 am on February 6 in Los Angeles CA 90064
 */
class Status extends \Magento\Shipping\Model\Tracking\Result\AbstractResult
{
    /**
     * @return array
     */
    public function getAllData()
    {
        return $this->_data;
    }
}
