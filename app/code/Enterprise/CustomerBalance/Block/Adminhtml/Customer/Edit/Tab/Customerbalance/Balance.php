<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_CustomerBalance_Block_Adminhtml_Customer_Edit_Tab_Customerbalance_Balance extends Magento_Adminhtml_Block_Template
{
    /**
     * Get delete orphan balances button
     *
     * @return string
     */
    public function getDeleteOrphanBalancesButton()
    {
        $customer = Mage::registry('current_customer');
        $balance = Mage::getModel('Enterprise_CustomerBalance_Model_Balance');
        if ($balance->getOrphanBalancesCount($customer->getId()) > 0) {
            return $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')->setData(array(
                'label'     => __('Delete Orphan Balances'),
                'onclick'   => 'setLocation(\'' . $this->getDeleteOrphanBalancesUrl() .'\')',
                'class'     => 'scalable delete',
            ))->toHtml();
        }
        return '';
    }

    /**
     * Get delete orphan balances url
     *
     * @return string
     */
    public function getDeleteOrphanBalancesUrl()
    {
        return $this->getUrl('*/customerbalance/deleteOrphanBalances', array('_current' => true, 'tab' => 'customer_info_tabs_customerbalance'));
    }
}
