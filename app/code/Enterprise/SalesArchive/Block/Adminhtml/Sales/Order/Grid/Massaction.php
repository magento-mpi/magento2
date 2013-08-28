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
 *  Add sales archiving to order's grid view massaction
 *  @deprecated
 */
class Enterprise_SalesArchive_Block_Adminhtml_Sales_Order_Grid_Massaction extends Magento_Adminhtml_Block_Widget_Grid_Massaction_Abstract
{
    /**
     * Before rendering html operations
     *
     * @return Enterprise_SalesArchive_Block_Adminhtml_Sales_Order_Grid_Massaction
     */
    protected function _beforeToHtml()
    {
        $isActive = Mage::getSingleton('Enterprise_SalesArchive_Model_Config')->isArchiveActive();
        if ($isActive && $this->_authorization->isAllowed('Enterprise_SalesArchive::add')) {
            $this->addItem('add_order_to_archive', array(
                 'label'=> __('Move to Archive'),
                 'url'  => $this->getUrl('*/sales_archive/massAdd'),
            ));
        }
        return parent::_beforeToHtml();
    }
}
