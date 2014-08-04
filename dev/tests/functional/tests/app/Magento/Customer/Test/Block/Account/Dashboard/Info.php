<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Block\Account\Dashboard;

use Mtf\Block\Block;

/**
 * Class Info
 * Main block on customer account page
 */
class Info extends Block
{
    /**
     * Css selector for Contact Information Edit Link
     *
     * @var string
     */
    protected $contactInfoEditLink = '.block-dashboard-info .box-information .action.edit';

    /**
     * Click on Contact Information Edit Link
     *
     * @return void
     */
    public function openEditContactInfo()
    {
        $this->_rootElement->find($this->contactInfoEditLink)->click();
    }
}
