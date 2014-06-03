<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * Payone payment block
 *
 * @author      Magento
 */
namespace Magento\Pbridge\Block\Adminhtml\Sales\Order\Create\Payone;

class Gate extends \Magento\Pbridge\Block\Adminhtml\Sales\Order\Create\AbstractCreate
{
    /**
     * Payone payment code
     *
     * @var string
     */
    protected $_code = 'payone_gate';
}
