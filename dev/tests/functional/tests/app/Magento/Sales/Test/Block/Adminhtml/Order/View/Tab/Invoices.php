<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\View\Tab;

/**
 * Class Invoices
 * Invoices tab
 */
class Invoices extends AbstractGridTab
{
    /**
     * Grid block css selector
     *
     * @var string
     */
    protected $grid = '#order_invoices';

    /**
     * Class name
     *
     * @var string
     */
    protected $class = 'Magento\Sales\Test\Block\Adminhtml\Order\View\Tab\Invoices\Grid';
}
