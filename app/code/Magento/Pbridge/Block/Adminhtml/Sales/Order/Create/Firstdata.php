<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * Firstdata payment block
 *
 * @author      Magento
 */
namespace Magento\Pbridge\Block\Adminhtml\Sales\Order\Create;

class Firstdata extends \Magento\Pbridge\Block\Adminhtml\Sales\Order\Create\AbstractCreate
{
    /**
     * Payone payment code
     *
     * @var string
     */
    protected $_code = 'pbridge_firstdata';
}
