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
 * Dibs payment block
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento
 */
namespace Magento\Pbridge\Block\Adminhtml\Sales\Order\Create;

class Dibs
    extends \Magento\Pbridge\Block\Adminhtml\Sales\Order\Create\AbstractCreate
{
    /**
     * Dibs payment code
     *
     * @var string
     */
    protected $_code = 'dibs';
}
