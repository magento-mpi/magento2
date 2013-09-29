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
 * Braintree payment block
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Pbridge\Block\Adminhtml\Sales\Order\Create\Braintree;

class Basic
    extends \Magento\Pbridge\Block\Adminhtml\Sales\Order\Create\AbstractCreate
{
    /**
     * Braintree payment code
     *
     * @var string
     */
    protected $_code = 'braintree_basic';
}
