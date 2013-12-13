<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Test\Block\Adminhtml\Promo\Quote\Edit;

use Magento\Backend\Test\Block\Widget\FormTabs;
use Magento\SalesRule\Test\Block\Adminhtml\Promo\Quote\Edit\Tab\Main;
use Magento\SalesRule\Test\Block\Adminhtml\Promo\Quote\Edit\Tab\Actions;
use Magento\SalesRule\Test\Block\Adminhtml\Promo\Quote\Edit\Tab\Conditions;

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

    /**
     * Set up tab classes
     * @var array
     */
    protected $tabClasses = array(
        Main::GROUP => '\Magento\SalesRule\Test\Block\Adminhtml\Promo\Quote\Edit\Tab\Main',
        Conditions::GROUP => '\Magento\SalesRule\Test\Block\Adminhtml\Promo\Quote\Edit\Tab\Conditions',
        Actions::GROUP => '\Magento\SalesRule\Test\Block\Adminhtml\Promo\Quote\Edit\Tab\Actions'
    );
}
