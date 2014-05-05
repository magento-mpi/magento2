<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Block\Adminhtml\Giftcardaccount\Edit;

use Magento\Backend\Test\Block\Widget\FormTabs;

/**
 * Class Form
 *
 * @package Magento\GiftCardAccount\Test\Block\Adminhtml\Giftcardaccount\Edit
 */
class Form extends FormTabs
{
    /**
     * {@inheritDoc}
     */
    protected $waitForSelector = 'div#giftcardaccount_info_tabs';

    /**
     * {@inheritDoc}
     */
    protected $waitForSelectorVisible = false;
}
