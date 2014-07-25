<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Block\Customer;

use Magento\Backend\Test\Block\GridPageActions;

/**
 * Class ListCustomer
 * Gift registry frontend actions block
 */
class ListCustomer extends GridPageActions
{
    /**
     * "Add New" button
     *
     * @var string
     */
    protected $addNewButton = '.add';
}
