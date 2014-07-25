<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\Block\Tooltip;

use Magento\Reward\Test\Block\Tooltip;

/**
 * Class Checkout
 * Checkout Tooltip block to get checkout cart messages about reward points
 */
class Checkout extends Tooltip
{
    /**
     * Message CSS selector on page
     *
     * @var string
     */
    protected $messageSelector = '.reward-checkout > :first-child';
}
