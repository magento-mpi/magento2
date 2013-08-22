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
class Magento_Pbridge_Block_Adminhtml_Sales_Order_Create_Payone_Gate
    extends Magento_Pbridge_Block_Adminhtml_Sales_Order_Create_Abstract
{
    /**
     * Payone payment code
     *
     * @var string
     */
    protected $_code = 'payone_gate';
}
