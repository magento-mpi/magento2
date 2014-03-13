<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Test\Block\Adminhtml\Promo\Quote\Edit;

use Magento\Backend\Test\Block\Widget\FormTabs;

/**
 * Class Form
 *
 * @package Magento\SalesRule\Test\Block\Adminhtml\Promo\Quote\Edit
 */
class Form extends FormTabs
{
    /**
     * {@inheritDoc}
     */
    protected $waitForSelector = 'div#promo_catalog_edit_tabs';

    /**
     * {@inheritDoc}
     */
    protected $waitForSelectorVisible = false;
}
