<?php
/**
 * Reward History grid
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_History_Grid
    extends Magento_Backend_Block_Widget_Grid
{
    /**
     * Prepare grid collection object
     *
     * @return Enterprise_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_History_Grid
     */
    protected function _prepareCollection()
    {
        $customerId = $this->getRequest()->getParam('id', 0);
        $this->getCollection()->addCustomerFilter($customerId);
        return parent::_prepareCollection();
    }
}
