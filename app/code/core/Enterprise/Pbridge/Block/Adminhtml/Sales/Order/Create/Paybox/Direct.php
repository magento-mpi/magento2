<?php
/**
 * {license}
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 */


/**
 * Paybox payment block
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @author      Magento
 */
class Enterprise_Pbridge_Block_Adminhtml_Sales_Order_Create_Paybox_Direct
    extends Enterprise_Pbridge_Block_Adminhtml_Sales_Order_Create_Abstract
{
    /**
     * Payone payment code
     *
     * @var string
     */
    protected $_code = 'paybox_direct';
}
