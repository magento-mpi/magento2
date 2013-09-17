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
 * Paybox payment block
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento
 */
class Magento_Pbridge_Block_Adminhtml_Sales_Order_Create_Paybox_Direct
    extends Magento_Pbridge_Block_Adminhtml_Sales_Order_Create_Abstract
{
    /**
     * Payone payment code
     *
     * @var string
     */
    protected $_code = 'paybox_direct';
}
