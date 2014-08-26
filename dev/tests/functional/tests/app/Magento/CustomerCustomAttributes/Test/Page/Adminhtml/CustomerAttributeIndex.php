<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerCustomAttributes\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class CustomerAttributeIndex
 */
class CustomerAttributeIndex extends BackendPage
{
    const MCA = 'admin/customer_attribute';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'messagesBlock' => [
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages',
            'strategy' => 'css selector',
        ],
        'gridPageActions' => [
            'class' => 'Magento\Backend\Test\Block\GridPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'customerCustomAttributesGrid' => [
            'class' => 'Magento\CustomerCustomAttributes\Test\Block\Adminhtml\Customer\Attribute\CustomerCustomAttributesGrid',
            'locator' => '[id="customerAttributeGrid"]',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }

    /**
     * @return \Magento\Backend\Test\Block\GridPageActions
     */
    public function getGridPageActions()
    {
        return $this->getBlockInstance('gridPageActions');
    }

    /**
     * @return \Magento\CustomerCustomAttributes\Test\Block\Adminhtml\Customer\Attribute\CustomerCustomAttributesGrid
     */
    public function getCustomerCustomAttributesGrid()
    {
        return $this->getBlockInstance('customerCustomAttributesGrid');
    }
}
