<?php
/**
 * {license_notice}
 *
 * @api
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Block;

use Mtf\Block\Block;

/**
 * Class DashboardHeaderPanel
 * Header panel of my account dashboard
 *
 * @package Magento\Customer\Test\Block
 */
class DashboardHeaderPanelTitle extends Block
{
    /**
     * Return title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_rootElement->getText();
    }
}
