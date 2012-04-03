<?php
/**
 * {license}
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 */


/**
 * Eway.Com.Au payment block
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Pbridge_Block_Adminhtml_Sales_Order_Create_Eway
    extends Enterprise_Pbridge_Block_Adminhtml_Sales_Order_Create_Abstract
{
    /**
     * Cybersource payment code
     *
     * @var string
     */
    protected $_code = 'eway_direct';
}
