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
 * Reward points balance container
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_Management_Balance
    extends Magento_Adminhtml_Block_Template
{
    protected $_template = 'customer/edit/management/balance.phtml';

    /**
     * Prepare layout.
     * Create balance grid block
     *
     * @return Magento_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_Management_Balance
     */
    protected function _prepareLayout()
    {
        if (!$this->_authorization->isAllowed(Magento_Reward_Helper_Data::XML_PATH_PERMISSION_BALANCE)
        ) {
            // unset template to get empty output

        } else {
            $grid = $this->getLayout()
                ->createBlock('Magento_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_Management_Balance_Grid');
            $this->setChild('grid', $grid);
        }
        return parent::_prepareLayout();
    }
}
