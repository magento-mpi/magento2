<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales archive grids containers
 *
 */
class Enterprise_SalesArchive_Block_Adminhtml_Sales_Archive_Order_Container extends Magento_Adminhtml_Block_Widget_Grid_Container
{
    protected function _prepareLayout()
    {
        $this->_removeButton('add');
        return $this;
    }
}
