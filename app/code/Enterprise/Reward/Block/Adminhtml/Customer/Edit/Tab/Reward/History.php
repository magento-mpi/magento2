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
 * Reward history container
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_History
    extends Magento_Adminhtml_Block_Template
{

    protected $_template = 'customer/edit/history.phtml';

    /**
     * Prepare layout.
     * Create history grid block
     *
     * @return Enterprise_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_History
     */
    protected function _prepareLayout()
    {
        $grid = $this->getLayout()
            ->createBlock('Enterprise_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_History_Grid')
            ->setCustomerId($this->getCustomerId());
        $this->setChild('grid', $grid);
        return parent::_prepareLayout();
    }
}
