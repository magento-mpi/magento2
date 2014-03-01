<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Block\Adminhtml\Order\Create\Totals;

/**
 * Tax Total Row Renderer
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Tax extends \Magento\Sales\Block\Adminhtml\Order\Create\Totals\DefaultTotals
{
    /**
     * Template
     *
     * @var string
     */
    protected $_template = 'order/create/totals/tax.phtml';
}
