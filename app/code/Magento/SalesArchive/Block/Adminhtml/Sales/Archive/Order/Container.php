<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales archive grids containers
 *
 */
class Magento_SalesArchive_Block_Adminhtml_Sales_Archive_Order_Container extends Magento_Adminhtml_Block_Widget_Grid_Container
{
    protected function _prepareLayout()
    {
        $this->_removeButton('add');
        return $this;
    }
}
