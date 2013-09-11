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
namespace Magento\Reward\Block\Adminhtml\Customer\Edit\Tab\Reward;

class Management
    extends \Magento\Adminhtml\Block\Template
{

    protected $_template = 'customer/edit/management.phtml';

    /**
     * Prepare layout
     *
     * @return \Magento\Reward\Block\Adminhtml\Customer\Edit\Tab\Reward\Management
     */
    protected function _prepareLayout()
    {
        $total = $this->getLayout()
            ->createBlock('\Magento\Reward\Block\Adminhtml\Customer\Edit\Tab\Reward\Management\Balance');

        $this->setChild('balance', $total);

        $update = $this->getLayout()
            ->createBlock('\Magento\Reward\Block\Adminhtml\Customer\Edit\Tab\Reward\Management\Update');

        $this->setChild('update', $update);

        return parent::_prepareLayout();
    }
}
