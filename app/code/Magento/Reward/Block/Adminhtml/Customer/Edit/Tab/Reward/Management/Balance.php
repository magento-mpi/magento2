<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Reward points balance container
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reward\Block\Adminhtml\Customer\Edit\Tab\Reward\Management;

class Balance extends \Magento\Backend\Block\Template
{
    /**
     * Reward balance management template
     *
     * @var string
     */
    protected $_template = 'customer/edit/management/balance.phtml';

    /**
     * Prepare layout.
     * Create balance grid block
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        if (!$this->_authorization->isAllowed(\Magento\Reward\Helper\Data::XML_PATH_PERMISSION_BALANCE)) {
            // unset template to get empty output
        } else {
            $grid = $this->getLayout()->createBlock(
                'Magento\Reward\Block\Adminhtml\Customer\Edit\Tab\Reward\Management\Balance\Grid'
            );
            $this->setChild('grid', $grid);
        }
        return parent::_prepareLayout();
    }
}
