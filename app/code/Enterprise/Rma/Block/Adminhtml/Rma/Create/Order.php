<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin RMA create order block
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Enterprise_Rma_Block_Adminhtml_Rma_Create_Order extends Enterprise_Rma_Block_Adminhtml_Rma_Create_Abstract
{
    /**
     * Get Header Text for Order Selection
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('Please Select Order');
    }
}
