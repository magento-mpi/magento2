<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reward management container
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_Management
    extends Magento_Adminhtml_Block_Template
{

    protected $_template = 'customer/edit/management.phtml';

    /**
     * Prepare layout
     *
     * @return Enterprise_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_Management
     */
    protected function _prepareLayout()
    {
        $total = $this->getLayout()
            ->createBlock('Enterprise_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_Management_Balance');

        $this->setChild('balance', $total);

        $update = $this->getLayout()
            ->createBlock('Enterprise_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_Management_Update');

        $this->setChild('update', $update);

        return parent::_prepareLayout();
    }
}
