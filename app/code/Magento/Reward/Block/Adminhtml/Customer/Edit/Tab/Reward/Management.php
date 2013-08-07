<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reward management container
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_Management
    extends Magento_Adminhtml_Block_Template
{

    protected $_template = 'customer/edit/management.phtml';

    /**
     * Prepare layout
     *
     * @return Magento_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_Management
     */
    protected function _prepareLayout()
    {
        $total = $this->getLayout()
            ->createBlock('Magento_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_Management_Balance');

        $this->setChild('balance', $total);

        $update = $this->getLayout()
            ->createBlock('Magento_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_Management_Update');

        $this->setChild('update', $update);

        return parent::_prepareLayout();
    }
}
