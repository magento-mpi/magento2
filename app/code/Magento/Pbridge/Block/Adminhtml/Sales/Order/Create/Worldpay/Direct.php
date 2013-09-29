<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Worldpay payment block
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Pbridge\Block\Adminhtml\Sales\Order\Create\Worldpay;

class Direct
    extends \Magento\Pbridge\Block\Adminhtml\Sales\Order\Create\AbstractCreate
{
    /**
     * Worldpay payment code
     *
     * @var string
     */
    protected $_code = 'worlpay_direct';
}
