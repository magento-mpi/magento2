<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Logging\Test\Block\Adminhtml;

use Mtf\Block\Block;

/**
 * Class Details
 * Backend admin logging details block
 */
class Details extends Block
{
    /**
     * Get Admin User Data from grid
     *
     * @return mixed
     */
    public function getData()
    {
        $data['aggregated_information'] =
            $this->_rootElement->find('#log_details_fieldset > table > tbody tr:nth-child(1) > td')->getText();
        $data['user_id'] =
            $this->_rootElement->find('#log_details_fieldset > table > tbody tr:nth-child(2) > td')->getText();
        $data['username'] =
            $this->_rootElement->find('#log_details_fieldset > table > tbody tr:nth-child(3) > td')->getText();
        preg_match('/\d+/', $data['user_id'], $matches);
        $data['user_id'] = intval($matches[0]);
        return $data;
    }

    /**
     * Check if Logging Details Grid visible
     *
     * @return bool
     */
    public function isLoggingDetailsGridVisible()
    {
        return $this->_rootElement->find('#loggingDetailsGrid')->isVisible();
    }
}
