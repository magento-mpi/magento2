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
 * Paypal UK Direct payment block
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Pbridge\Block\Adminhtml\Sales\Order\Create;

class Paypaluk extends \Magento\Pbridge\Block\Adminhtml\Sales\Order\Create\Paypal
{
    /**
     * Paypal UK payment code
     *
     * @var string
     */
    protected $_code = \Magento\Paypal\Model\Config::METHOD_WPP_PE_DIRECT;
}
