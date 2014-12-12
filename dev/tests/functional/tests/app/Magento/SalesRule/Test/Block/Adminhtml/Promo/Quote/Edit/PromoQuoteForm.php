<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\SalesRule\Test\Block\Adminhtml\Promo\Quote\Edit;

use Magento\Backend\Test\Block\Widget\FormTabs;

/**
 * Class PromoQuoteForm
 */
class PromoQuoteForm extends FormTabs
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
