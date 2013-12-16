<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Page;

use Mtf\Page\Page;

/**
 * Class CustomerAccountLogout
 * Customer frontend logout page.
 *
 * @package Magento\Customer\Test\Page
 */
class CustomerAccountLogout extends Page
{
    /**
     * URL for customer logout
     */
    const MCA = 'customer/account/logout';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;
    }
}
