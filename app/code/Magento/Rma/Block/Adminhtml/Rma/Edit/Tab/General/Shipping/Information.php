<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shipment Information block
 *
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rma_Block_Adminhtml_Rma_Edit_Tab_General_Shipping_Information
    extends Magento_Rma_Block_Adminhtml_Rma_Edit_Tab_General_Shipping_Packaging
{
    /**
     * Constructor
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('edit/shipping/information.phtml');
    }
}
