<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * Eway.Com.Au payment block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Pbridge\Block\Adminhtml\Sales\Order\Create;

class Eway extends \Magento\Pbridge\Block\Adminhtml\Sales\Order\Create\AbstractCreate
{
    /**
     * Cybersource payment code
     *
     * @var string
     */
    protected $_code = 'eway_direct';
}
