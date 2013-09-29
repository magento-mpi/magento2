<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * Payone payment block
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento
 */
namespace Magento\Pbridge\Block\Adminhtml\Sales\Order\Create\Payone;

class Gate
    extends \Magento\Pbridge\Block\Adminhtml\Sales\Order\Create\AbstractCreate
{
    /**
     * Payone payment code
     *
     * @var string
     */
    protected $_code = 'payone_gate';
}
