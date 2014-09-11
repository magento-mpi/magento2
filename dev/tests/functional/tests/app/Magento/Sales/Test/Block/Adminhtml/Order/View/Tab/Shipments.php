<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\View\Tab;

/**
 * Class Shipments
 * Shipments tab
 */
class Shipments extends AbstractGridTab
{
    /**
     * Grid block css selector
     *
     * @var string
     */
    protected $grid = '#order_shipments';

    /**
     * Class name
     *
     * @var string
     */
    protected $class = 'Magento\Sales\Test\Block\Adminhtml\Order\View\Tab\Shipments\Grid';
}
