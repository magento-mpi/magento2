<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reward management container
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reward\Block\Adminhtml\Customer\Edit\Tab\Reward;

class Management extends \Magento\Backend\Block\Template
{
    /**
     * Reward management template
     *
     * @var string
     */
    protected $_template = 'customer/edit/management.phtml';

    /**
     * Prepare layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $total = $this->getLayout()->createBlock(
            'Magento\Reward\Block\Adminhtml\Customer\Edit\Tab\Reward\Management\Balance'
        );

        $this->setChild('balance', $total);

        $update = $this->getLayout()->createBlock(
            'Magento\Reward\Block\Adminhtml\Customer\Edit\Tab\Reward\Management\Update',
            '',
            [
                'data' => [
                    'target_form' => $this->getData('target_form')
                ]
            ]
        );

        $this->setChild('update', $update);

        return parent::_prepareLayout();
    }
}
